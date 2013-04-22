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
 * Class ModuleEventReaderEdit 
 */
class ModuleEventReaderEdit extends Events
{

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_event_ReaderEditLink';
	
	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE') {
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### EVENT READER EDIT LINK ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}
		
		// Return if no event has been specified
		if (!$this->Input->get('events')) {
			return '';
		}
		
		$this->cal_calendar = $this->sortOutProtected(deserialize($this->cal_calendar));

		// Return if there are no calendars
		if (!is_array($this->cal_calendar) || count($this->cal_calendar) < 1)
		{
			return '';
		}
		return parent::generate();
	}


	/**
	 * Generate module
	 */
	protected function compile()
	{
		$this->Template = new FrontendTemplate($this->strTemplate);
		$this->Template->editref = '';
		
		if (!FE_USER_LOGGED_IN) {	
			$this->Template->error = 'Not logged in.';
			return ;
		}
		
		// FE user is logged in
		$this->import('FrontendUser', 'User');
		
		// Get current event
		$objEvent = $this->Database->prepare("SELECT *, author AS authorId, (SELECT title FROM tl_calendar WHERE tl_calendar.id=tl_calendar_events.pid) AS calendar, (SELECT name FROM tl_user WHERE id=author) author FROM tl_calendar_events WHERE pid IN(" . implode(',', array_map('intval', $this->cal_calendar)) . ") AND (id=? OR alias=?)" . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<?) AND (stop='' OR stop>?) AND published=1" : ""))
								   ->limit(1)
								   ->execute((is_numeric($this->Input->get('events')) ? $this->Input->get('events') : 0), $this->Input->get('events'), $time, $time);

		if ($objEvent->numRows < 1) {			
			return;
		}
		
		// get Calender with PID
		$pid = $objEvent->pid;
		$objCalendar = $this->Database->prepare("SELECT * FROM tl_calendar WHERE id=?")
                         			->limit(1)
									->execute($pid);

									
		if ($objCalendar->numRows < 1) {
			return; // No calendar found
		}
		if ($objCalendar->AllowEdit) {
			// Calendar allows editing
			// check user rights
			
			// Get Groups which are allowed to edit events in this calendar
			$groups = deserialize($objCalendar->caledit_groups);
			if (!is_array($groups) || count($groups) < 1 || count(array_intersect($groups, $this->User->groups)) < 1)
			{				
				$AuthorizedUser = FALSE;
			}
			else {	
				$AuthorizedUser = TRUE;
			}

			// Get Admin-Groups which are allowed to edit events in this calendar
			// (Admins are allowed to edit events even if the "only owner"-setting is checked)
			$admin_groups = deserialize($objCalendar->caledit_adminGroup);
			if (!is_array($admin_groups) || count($admin_groups) < 1 || count(array_intersect($admin_groups, $this->User->groups)) < 1)
			{				
				$UserIsAdmin = FALSE;
			}
			else {			
				$UserIsAdmin = TRUE;
			}

			if ($AuthorizedUser || $UserIsAdmin )
			{
				// user is allowed to edit events here
				$AllowAllEdits =  ($UserIsAdmin) || (!$objCalendar->caledit_onlyUser);				
				$AddEditLinks = (($AllowAllEdits || ($objEvent->FE_User == $this->User->id)) && (!$objEvent->disable_editing));				
			} else {
				$AddEditLinks = FALSE;
			}
			
			if ($AddEditLinks) {				
				// get the JumpToEdit-Page for this calendar
				$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=(SELECT caledit_jumpTo FROM tl_calendar WHERE id=?)")
								  ->limit(1)
								  ->execute($objCalendar->id);
				if ($objPage->numRows) {
					$strUrl = $this->generateFrontendUrl($objPage->row(), '');
				}
				else {
					$strUrl = $this->Environment->request;	
				}
					
				$this->Template->editref = $strUrl.'?edit='.$objEvent->id;
				$this->Template->editLabel = $GLOBALS['TL_LANG']['MSC']['caledit_editLabel'];
				$this->Template->editTitle = $GLOBALS['TL_LANG']['MSC']['caledit_editTitle'];

			} else {
				$this->Template->error_class = 'tl_error';
				$this->Template->error = 'Editing not allowed.';
			}
		
		} else {
			$this->Template->error_class = 'tl_error';
			$this->Template->error = 'Editing not allowed.';
			return ;
		}		
	}
}
