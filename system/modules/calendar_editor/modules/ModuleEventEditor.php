<?php

/**
 * Contao Open Source CMS
 * Copyright (C) 2005-2013 Leo Feyer
 *
 * PHP version 5
 *
 * @package		CalendarEditor
 * @author		Daniel Gaussmann <mail@gausi.de>
 * @author		Arthur Varkentin <mail@varkentin.de> 
 * @copyright	Daniel Gaussmann, Arthur Varkentin 2011-2013
 * @license		http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 * @version		3.0.0 beta
 */

/**
 * Class ModuleEventEditor
 */
class ModuleEventEditor extends Events {
    /**
     * Template
     *
     * @var string
     */
    protected $strTemplate = 'eventEdit_default';

    /**
     * add the selected TinyMCE into the header of the page
     */
    public function addTinyMCE($str)
    {
        if (!empty($str)) {
            $strFile = sprintf('%s/system/config/%s.php', TL_ROOT, $str);
            $this->rteFields = 'ctrl_teaser';
            $this->language = 'en';
            // Fallback to English if the user language is not supported
            if (file_exists(TL_ROOT . '/plugins/tinyMCE/langs/' . $GLOBALS['TL_LANGUAGE'] . '.js')) {
                $this->language = $GLOBALS['TL_LANGUAGE'];
            }

            if (!file_exists($strFile)) {
                echo (sprintf('Cannot find rich text editor configuration file "%s"', $strFile));
            }else {
                ob_start();
                include($strFile);
                $GLOBALS['TL_HEAD']['rte'] = ob_get_contents();
                ob_end_clean();
                $GLOBALS['TL_JAVASCRIPT']['rte'] = 'contao/contao.js';
            }
        }
    }

    /**
     * Check whether the FE user is Admin for the calendar
     */
    public function IsUserAdminForCalendar($user, $objCalendar)
    {
        $admin_groups = deserialize($objCalendar->caledit_adminGroup);
        return (is_array($admin_groups) &count($admin_groups) > 0 && count(array_intersect($admin_groups, $user->groups)) > 0);
    }

    /**
     * Check whether the user is allowed to edit events in the calendar
     */
    public function IsUserAllowedToEditCalendar($user, $objCalendar)
    {
        if ($objCalendar->AllowEdit) {
            // is User an Admin?
            if ($this->IsUserAdminForCalendar($user, $objCalendar)) {
                return true;
            }
            // Get Groups which are allowed to edit events in this calendar
            $groups = deserialize($objCalendar->caledit_groups);
            if (is_array($groups) && count($groups) > 0 && count(array_intersect($groups, $user->groups)) > 0) {
                return true;
            }
        }
        return $FALSE;
    }
	
	public function UserIsToAddCalendar($user, $pid)
	{
		$objCalendar = $this->Database->prepare("SELECT * FROM tl_calendar WHERE id=?")
										->limit(1)
										->execute($pid);
		if ($objCalendar->numRows < 1) {
            return false;
		}
		return ($this->IsUserAllowedToEditCalendar($user, $objCalendar));
	}
	

    /**
     * Get the calendars the user is allowed to edit
     * These calendars will appear in the selection-field in the edit-form (if there is not only one)
     */
    public function getCalendars($user)
    {
        $calendars = array();

        foreach ($this->cal_calendar as $currCal) {
            $objCalendar = $this->Database->prepare("SELECT * FROM tl_calendar WHERE id=?")->limit(1)->execute($currCal);
            if ($this->IsUserAllowedToEditCalendar($user, $objCalendar)) {
                $calendars[] = array(
                    'id' => $objCalendar->id,
                    'name' => $objCalendar->title);
            }
        }
        return($calendars);
    }

    /**
     * check, whether the user is allowed to edit the specified Event
     * This is called when the user has general access to at least one calendar
     * But: We need to check whether he is allowed to edit this special event
     *       - is he in the group/admingroup in the event's calendar?
     *       - is he the owner of the event or !caledit_onlyUser
     */
    public function checkUserEditRights($user, $eventID, $CurrentObjectData)
    {
        // if no event is specified: ok, FE user can add new events :D
        if (!$eventID) {
            return true;
        }
        // get the calendar of the event
		$cpid = $CurrentObjectData->pid;
        $objCalendar = $this->Database->prepare("SELECT * FROM tl_calendar WHERE id=?")->limit(1)->execute($cpid);
        if ($objCalendar->numRows < 1) {
            return false; // Event not found or something else is wrong
        }
        // check calendar settings (just as in getCalendars)
        if ($this->IsUserAllowedToEditCalendar($user, $objCalendar)) {
            // ok, user is allowed to edit events here.
            // however, he is not necessary the owner of the event.
            //$objEvent = $this->Database->prepare("SELECT * FROM tl_calendar_events WHERE id=?")->limit(1)->execute($eventID);
            if ($CurrentObjectData->numRows < 1) {
                return false; // Event not found or something else is wrong
            }
            // if the editing is disabled in the BE: Deny editing in the FE
            if ($CurrentObjectData->disable_editing) {
                return false;
            }

            return ((!$objCalendar->caledit_onlyUser) ||
                ($this->IsUserAdminForCalendar($user, $objCalendar) || ($user->id == $CurrentObjectData->FE_User))
                );
        }else {
            return false; // user is not allowed to edit events here
        }
    }

    public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### EVENT EDITOR ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }

        $this->cal_calendar = $this->sortOutProtected(deserialize($this->cal_calendar));
        // Return if there are no calendars
        if (!is_array($this->cal_calendar) || count($this->cal_calendar) < 1) {
            return '';
        }

        return parent::generate();
    }

    
    public function generateAlias($varValue, $newID)
    {
		// Generate new alias from varvalue (here: the title)
		$varValue = standardize($varValue);
		
		$objAlias = $this->Database->prepare("SELECT id FROM tl_calendar_events WHERE alias=?")
						->execute($varValue);
				 
		// Add ID to alias, if it already exists
		if ($objAlias->numRows) {
			$varValue .= '-' . $newID;
		}		
		return $varValue;
    }
    
	 
	 
	public function PutIntoDB($eventData, $OldId)
    {        
		// get current max. ID in tl_calendar_events (needed for new alias)
		$maxI = $this->Database->prepare("SELECT MAX(id) as id FROM tl_calendar_events")
								->limit(1)
								->execute();
		$newID = $maxI->id + 1;

		if (!$OldId) {
			// create new alias
			$alias = $this->generateAlias($eventData['title'], $newID);
		}
		else {
			// use existing alias
			$alias = $eventData['alias'];
		}
		
		
		$search  = array('&#60;','&#61;','&#62;');
		$replace = array('<','=', '>');
		
		$startDate = strtotime($eventData['startDate']);	
		
		$arrEvent = array
				(
				 'pid' => $eventData['pid'],
				 'alias' => $alias,
				 //'author' => 0,  // the Backend-user
				 'tstamp' => $startDate,
				 'title' => $eventData['title'],
				 'teaser' => $eventData['teaser'],
				 'startDate' => $startDate,
				 'cssClass' => $eventData['css'],
				 'published' => $eventData['published'],
				 'FE_User'   => $eventData['FE_User']
				);			

			// Dealing with empty enddates, Start/endtimes ...
			if (trim($eventData['endDate']) != '') {
				// an enddate is given
				$endDate = strtotime($eventData['endDate']);
				$arrEvent['endDate'] = $endDate;
				$endDateStr = $eventData['endDate'];
			}
			else {
				// no enddate is given. => Set it to NULL
				$arrEvent['endDate'] = NULL;
				// needed later
				$endDateStr = $eventData['startDate'];
            }
                
				
			if (trim($eventData['startTime']) == '') {
					// Dont add time
					$useTime = '';
					$arrEvent['addTime'] = '';
					$arrEvent['startTime'] = $startDate;
			} else {
				// Add time to the event
				$useTime = '1';
				$startTime = strtotime($eventData['startDate'].' '.$eventData['startTime']);
				$arrEvent['addTime'] = '1';
				$arrEvent['startTime'] = $startTime;
			}

             
			if (trim($eventData['endTime']) == '') {
				// if no endtime is given: set andtime = starttime
				$endTime = strtotime($endDateStr.' '.$eventData['startTime']);
				$arrEvent['endTime'] = $endTime;
			}
			else {
				if ($useTime == '1')
				{
					$endTime = strtotime($endDateStr.' '.$eventData['endTime']);
					$arrEvent['endTime'] = $endTime;
				}
			}
				
			if (empty($OldId)) {
				// create new entry
				$objInsert = $this->Database->prepare('INSERT INTO tl_calendar_events %s')->set($arrEvent)->execute();
			}
			else {
				// update existing entry
                $objUpdate = $this->Database->prepare("UPDATE tl_calendar_events %s WHERE id=?")->set($arrEvent)->execute($OldId);
			}          
    }

    /**
     * Generate module
     */
    protected function compile()
    {
        // Add TinyMCE-Stuff to header
        $this->addTinyMCE($this->caledit_tinMCEtemplate);
        // Check for "add" or "edit"
        $newDate = $this->Input->get('add');
		$editID = '';
        $editID = $this->Input->get('edit');
        // check later for empty($newDate) // empty($editID)
        $this->import('FrontendUser', 'User');

        if (!FE_USER_LOGGED_IN) {
            $fatalError = $GLOBALS['TL_LANG']['MSC']['caledit_NotRegisteredUser'];
        } else {
            $AllowedCalendars = $this->getCalendars($this->User);			
            if (count($AllowedCalendars) == 0) {
                $fatalError = $GLOBALS['TL_LANG']['MSC']['caledit_UnauthorizedUser'];
            } else {
				$currentEventObject = $this->Database->prepare("SELECT * FROM tl_calendar_events WHERE id=?")->limit(1)->execute($editID);
                $AuthorizedUser = (bool) $this->checkUserEditRights($this->User, $editID, $currentEventObject);

                if (!$AuthorizedUser) {
                    $fatalError = $GLOBALS['TL_LANG']['MSC']['caledit_UnauthorizedUserThis'];
                }
            }
        }

        $this->strTemplate = $this->caledit_template;
        $this->Template = new FrontendTemplate($this->strTemplate);

        if ($fatalError) {         
            $this->Template->FatalError = $fatalError;
            return ;
        }
		           
		// ok, the user is an authorized user
		// 1. Get Data from post/get
		$startDate = $newDate;
		$postStart = \Input::post('startdate');
		if (!empty($postStart)) {
			$startDate = $postStart;
		}
		// todo: bei "edit" erst zeug aus der DB holen
		
		if ($editID){
			// Fill fields with data from $currentEventObject
			$startDate = $currentEventObject->startDate;
			
			$endDate = $currentEventObject->endDate;		
			if ($currentEventObject->addTime){
				$starttime = $currentEventObject->startTime;
				$endtime = $currentEventObject->endTime;
			} else {
				$starttime = '';
				$endtime = '';
			}			
			$title = $currentEventObject->title;
			$teaser = $currentEventObject->teaser;
			$css = $currentEventObject->cssClass;
			$pid = $currentEventObject->pid;			
			$published = $currentEventObject->published;	
			$alias = $currentEventObject->alias;
			
			
			// get the JumpTo-Page for this calendar
			
			$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=(SELECT jumpTo FROM tl_calendar WHERE id=?)")
									  ->limit(1)
									  ->execute($pid);
									  
									  
			//$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
			//				  ->limit(1)
			//				  ->execute($this->jumpTo);
			if ($objPage->numRows) {
				$showUrl = $this->generateFrontendUrl($objPage->row(), '/events/%s');
			}
			else {
				$showUrl = $this->Environment->request;
			}			
			$this->Template->CurrentTitle = $currentEventObject->title;
			$this->Template->CurrentDate = $this->parseDate($GLOBALS['TL_CONFIG']['dateFormat'], $currentEventObject->startDate);
			
			
			$this->Template->CurrentEventLink = $this->generateEventUrl($currentEventObject, $showUrl);
			if ($published == '') {
				$this->Template->CurrentPublishedInfo = $GLOBALS['TL_LANG']['MSC']['caledit_unpublishedEvent'];
			} 
			else {
				$this->Template->CurrentPublishedInfo = $GLOBALS['TL_LANG']['MSC']['caledit_publishedEvent'];
			}
			$this->Template->CurrentPublished = $published;
			
			
						
			
			if ($published && !$this->caledit_allowPublish){
				// editing a published event with no publish-rights
				// will hide the event again
				$published = '';
			}
		} else {
			$this->Template->CurrentPublishedInfo =	 $GLOBALS['TL_LANG']['MSC']['caledit_newEvent'];
		}
		
				
		// after this: Overwrite it with the post data 
		if (\Input::post('FORM_SUBMIT') == 'caledit_submit') {		
			$startDate = \Input::post('startdate');
			$endDate = \Input::post('enddate');
			$starttime = \Input::post('starttime');
			$endtime = \Input::post('endtime');
			$title = \Input::post('title');
			$teaser = \Input::post('teaser');
			$css = \Input::post('css');
			$pid = \Input::post('pid');
			$published = \Input::post('published');
						
			
			if ($published && !$this->caledit_allowPublish){
				// this should never happen, except the FE user is manipulating
				// the POST with some evil HackerToolz
				$fatalError = 'You are not authorized to publish events.';
				$this->Template->FatalError = $fatalError;
				return ;
			}
			
			if (empty($pid)) {
				// set default value 
				$pid = $AllowedCalendars[0]['id'];
			};
			if (!$this->UserIsToAddCalendar($this->User, $pid)){
				// this should never happen, except the FE user is manipulating
				// the POST with some evil HackerToolz. ;-)
				$fatalError = 'You are not authorized to create events in this calendar.';
				$this->Template->FatalError = $fatalError;
				return ;
			}
		}
				
		$mandfields = deserialize($this->caledit_mandatoryfields);
		$mandTeaser = (is_array($mandfields) && array_intersect(array('teaser') , $mandfields));
		$mandStarttime = (is_array($mandfields) && array_intersect(array('starttime'), $mandfields));
		$mandCss = (is_array($mandfields) && array_intersect(array('css') , $mandfields));
		// fill template with fields ...
		$fields = array();
		// use calendarfield if installed
		if (in_array('calendarfield', $this->Config->getActiveModules())) 
		{
			$fields['startDate'] = array(
				'name' => 'startDate',
				'label' => $GLOBALS['TL_LANG']['MSC']['caledit_startdate'],
				'inputType' => 'calendar',
				'value' => $startDate,
				'eval' => array('rgxp' => 'date', 'mandatory' => true, 'dateImage'=>true)
				);

			$fields['endDate'] = array(
				'name' => 'endDate',
				'label' => $GLOBALS['TL_LANG']['MSC']['caledit_enddate'],
				'inputType' => 'calendar',
				'value' => $endDate,
				'eval' => array('rgxp' => 'date', 'mandatory' => false, 'maxlength' => 128, 'decodeEntities' => true, 'dateImage'=>true)
				);
		} else {
			$fields['startDate'] = array(
				'name' => 'startDate',
				'label' => $GLOBALS['TL_LANG']['MSC']['caledit_startdate'],
				'inputType' => 'text',
				'value' => $startDate,
				'eval' => array('rgxp' => 'date', 'mandatory' => true)
				);

			$fields['endDate'] = array(
				'name' => 'endDate',
				'label' => $GLOBALS['TL_LANG']['MSC']['caledit_enddate'],
				'inputType' => 'text',
				'value' => $endDate,
				'eval' => array('rgxp' => 'date', 'mandatory' => false, 'maxlength' => 128, 'decodeEntities' => true)
				);
		}
		$fields['startTime'] = array(
			'name' => 'startTime',
			'label' => $GLOBALS['TL_LANG']['MSC']['caledit_starttime'],
			'inputType' => 'text',
			'value' => $starttime,
			'eval' => array('rgxp' => 'time', 'mandatory' => $mandStarttime, 'maxlength' => 128, 'decodeEntities' => true)
			);

		$fields['endTime'] = array(
			'name' => 'endTime',
			'label' => $GLOBALS['TL_LANG']['MSC']['caledit_endtime'],
			'inputType' => 'text',
			'value' => $endtime,
			'eval' => array('rgxp' => 'time', 'mandatory' => false, 'maxlength' => 128, 'decodeEntities' => true)
			);

		$fields['title'] = array(
			'name' => 'title',
			'label' => $GLOBALS['TL_LANG']['MSC']['caledit_title'],
			'inputType' => 'text',
			'value' => $title,
			'eval' => array('mandatory' => true, 'maxlength' => 255, 'decodeEntities' => true)
			);

		$fields['teaser'] = array(
			'name' => 'teaser',
			'label' => $GLOBALS['TL_LANG']['MSC']['caledit_teaser'],
			'inputType' => 'textarea',
			'value' => $teaser,
			'eval' => array('mandatory' => $mandTeaser, 'decodeEntities' => true)
			);
		
						
		if (count($AllowedCalendars) == 1)
		{
		 /*	// do not show a Calendar-Selector
			$fields['pid'] = array(
			'name' => 'pid',
			'label' => $GLOBALS['TL_LANG']['MSC']['caledit_pid'],
			'inputType' => 'hidden',
			'value' => $pid			
			);				
			*/
		} else {
			// Show allowed Calendars in a select-field
			$pref = array();
			$popt = array();
			foreach ($AllowedCalendars as $cal) {
				$popt[] = $cal['id'];
				$pref[$cal['id']] = $cal['name'];
			}
			$fields['pid'] = array(
				'name' => 'pid',
				'label' => $GLOBALS['TL_LANG']['MSC']['caledit_pid'],
				'inputType' => 'select',
				'options' => $popt, 
				'value' => $pid,
				'reference' => $pref,
				'eval' => array('mandatory' => true)
				);			
		}

		$xx = $this->caledit_alternateCSSLabel;
		$cssLabel = (empty($xx)) ? $GLOBALS['TL_LANG']['MSC']['caledit_css'] : $this->caledit_alternateCSSLabel;

		if ($this->caledit_usePredefinedCss) {
			$cssValues = deserialize($this->caledit_cssValues);

			$ref = array();
			$opt = array();

			foreach ($cssValues as $cssv) {
				$opt[] = $cssv['1'];
				$ref[$cssv['1']] = $cssv['0'];
			}

			$fields['css'] = array(
				'name' => 'css',
				'label' => $cssLabel,
				'inputType' => 'select',
				'options' => $opt, 
				'value' => $css,
				'reference' => $ref,
				'eval' => array('mandatory' => $mandCss, 'includeBlankOption' => true, 'maxlength' => 128, 'decodeEntities' => true)
				);
		} else {
			$fields['css'] = array(
				'name' => 'css',
				'label' => $cssLabel,
				'inputType' => 'text',
				'value' => $css,
				'eval' => array('mandatory' => $mandCss, 'maxlength' => 128, 'decodeEntities' => true)
				);
		}
		
		if ($this->caledit_allowPublish) {
			$fields['published'] = array(
				'name' => 'published',
				'label' =>  $GLOBALS['TL_LANG']['MSC']['caledit_published'],
				'inputType' => 'checkbox',				
				'value' => $published
				);
			$fields['published']['options']['1'] = $GLOBALS['TL_LANG']['MSC']['caledit_published'];
		}
		
		//HOOK: Add custom fields
		if (isset($GLOBALS['TL_HOOKS']['buildCalendarEditForm']) && is_array($GLOBALS['TL_HOOKS']['buildCalendarEditForm']))
		{
			foreach ($GLOBALS['TL_HOOKS']['buildCalendarEditForm'] as $callback)
			{
				$this->import($callback[0]);
				$arrHookResult = $this->$callback[0]->$callback[1]($this->$NewEventData, $fields, $currentEventObject, $editID);
				
				$NewEventData = $arrHookResult['NewEventData'];
				$fields = $arrHookResult['fields'];
			}
		}

		$arrWidgets = array();
		// Initialize widgets
		$doNotSubmit = false;
		foreach ($fields as $arrField) {
			$strClass = $GLOBALS['TL_FFL'][$arrField['inputType']];

			$arrField['eval']['required'] = $arrField['eval']['mandatory'];
			$objWidget = new $strClass($this->prepareForWidget($arrField, $arrField['name'], $arrField['value']));
			// Validate widget
			if (\Input::post('FORM_SUBMIT') == 'caledit_submit') {
				$objWidget->validate();
				if ($objWidget->hasErrors()) {
					$doNotSubmit = true;
				}
			}
			$arrWidgets[$arrField['name']] = $objWidget;
		}
					
		$this->Template->submit = $GLOBALS['TL_LANG']['MSC']['caledit_saveData'];
		$this->Template->calendars = $AllowedCalendars;
		
		if ((!$doNotSubmit) && (\Input::post('FORM_SUBMIT') == 'caledit_submit')){
			// everything seems to be ok, so we can add the POST Data
			// into the Database
			$NewEventData = array(
						'startDate' => $startDate,
						'endDate'   => $endDate,
						'startTime' => $starttime,
						'endTime'   => $endtime,						
						'title'     => $title,
						'teaser'    => $teaser,
						'css'       => $css,
						'pid'       => $pid,
						'published' => $published,
						'FE_User'   => $this->User->id,
						'alias'     => $alias						
						);
									
						
			$this->PutIntoDB($NewEventData, $editID);
			
			// after this: jump to "jumpTo-Page"												
			$jt = preg_replace('/\?.*$/i', '', $this->Environment->request);
			// Get current "jumpTo" page
			$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
									  ->limit(1)
									  ->execute($this->jumpTo);

			if ($objPage->numRows)
			{
				$jt = $this->generateFrontendUrl($objPage->row());
			}				
			$this->redirect($jt, 301);
		} else
		{
			// Do NOT Submit
			if (\Input::post('FORM_SUBMIT') == 'caledit_submit')
			{
				$this->Template->InfoClass = 'tl_error';
				$this->Template->InfoMessage = 'Ein Fehler ist aufgetreten.';							
			} 
			$this->Template->fields = $arrWidgets;
        
        }
		
		
		
		// weitermachen
        // if (!FE_USER_LOGGED_IN)
        // {
        // $this->strTemplate = 'event_AccessDenied';
        // }
        // variablen f�r datuim, enddatum, startzeit, endzeit, titel, ... f�llen, die ggf. sp�ter ans Template geleitet werden.
        

        /*
                 	ToDo:
                         - Kalender checken, ob der user in einem der Kalender �berhaupt was darf
                         - Url parsen: ist da ein Add? Ein Edit?
                         - Wenn Edit: Funktion Edit aufrufen
                         - Wenn Add: Add aufrufen
                         - Wenn nix: Add aufrufen mit aktuellem Datum/nix


                 */
        // TextFeld muss die ID   $strRTE;   bekommen (was galt oben im Tiny-Gedoens steht)
        // /  if (\Input::post('action') == 'send') {   ... in DB eintragen
    }
}
