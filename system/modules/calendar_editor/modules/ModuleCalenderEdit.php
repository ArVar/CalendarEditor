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
 * Class ModuleCalenderEdit
 */
class ModuleCalenderEdit extends ModuleCalendar
{

	// check weather the current FE User is allowed to edit any of the calendars
	public function IsAddingAllowed($arrCalendars)
	{
		if (!FE_USER_LOGGED_IN) {
			return FALSE;
		}

		$this->import('FrontendUser', 'User');
		foreach ($arrCalendars as $id)
		{
			// get properties of this calendar
			$objCalendar = $this->Database->prepare("SELECT * FROM tl_calendar WHERE id=?")
										->limit(1)
										->execute($id);
			if ($objCalendar->AllowEdit)
			{
				// Get Groups which are allowed to edit events in this calendar
				$groups = deserialize($objCalendar->caledit_groups);
				if (is_array($groups) && count($groups) > 0 && count(array_intersect($groups, $this->User->groups)) > 0)
				{
					return TRUE;
				}

				// Get Admin-Groups which are allowed to edit events in this calendar
				// (Admins are allowed to edit events even if the "only owner"-setting is checked)
				$admin_groups = deserialize($objCalendar->caledit_adminGroup);
				if (is_array($admin_groups) && count($admin_groups) > 0 && count(array_intersect($admin_groups, $this->User->groups)) > 0)
				{
					return  TRUE;
				}
			}
		}
		return FALSE;
	}


	// overwrite the compileWeeks-Method from ModuleCalendar
	protected function compileWeeks()
	{
		$intDaysInMonth = date('t', $this->Date->monthBegin);
		$intFirstDayOffset = date('w', $this->Date->monthBegin) - $this->cal_startDay;

		if ($intFirstDayOffset < 0) {
			$intFirstDayOffset += 7;
		}

		$AddingAllowed = $this->IsAddingAllowed($this->cal_calendar);

		if ($AddingAllowed){
			// get the JumpToAdd-Page for this calendar
			$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=?")
							  ->limit(1)
							  ->execute($this->caledit_add_jumpTo);
			if ($objPage->numRows) {
				$addUrl = $this->generateFrontendUrl($objPage->row(), '');
			}
			else {
				$addUrl = $this->Environment->request;
			}
		}
		$intYear = date('Y', $this->Date->tstamp);
		$intMonth = date('m', $this->Date->tstamp);
		
		$intColumnCount = -1;
		$intNumberOfRows = ceil(($intDaysInMonth + $intFirstDayOffset) / 7);
		$arrAllEvents = $this->getAllEvents($this->cal_calendar, $this->Date->monthBegin, $this->Date->monthEnd);
		$arrDays = array();

		$dateformat = $GLOBALS['TL_CONFIG']['dateFormat'];	
		
		// Compile days
		for ($i=1; $i<=($intNumberOfRows * 7); $i++)
		{
			$intWeek = floor(++$intColumnCount / 7);
			$intDay = $i - $intFirstDayOffset;
			$intCurrentDay = ($i + $this->cal_startDay) % 7;

			$strWeekClass = 'week_' . $intWeek;
			$strWeekClass .= ($intWeek == 0) ? ' first' : '';
			$strWeekClass .= ($intWeek == ($intNumberOfRows - 1)) ? ' last' : '';

			$strClass = ($intCurrentDay < 2) ? ' weekend' : '';
			$strClass .= ($i == 1 || $i == 8 || $i == 15 || $i == 22 || $i == 29 || $i == 36) ? ' col_first' : '';
			$strClass .= ($i == 7 || $i == 14 || $i == 21 || $i == 28 || $i == 35 || $i == 42) ? ' col_last' : '';

			// Empty cell
			if ($intDay < 1 || $intDay > $intDaysInMonth) {
				$arrDays[$strWeekClass][$i]['label'] = '&nbsp;';
				$arrDays[$strWeekClass][$i]['class'] = 'days empty' . $strClass ;
				$arrDays[$strWeekClass][$i]['events'] = array();

				continue;
			}

			$intKey = date('Ym', $this->Date->tstamp) . ((strlen($intDay) < 2) ? '0' . $intDay : $intDay);
			$strClass .= ($intKey == date('Ymd')) ? ' today' : '';

			$arrDays[$strWeekClass][$i]['addLabel'] = $GLOBALS['TL_LANG']['MSC']['caledit_addLabel'];
			$arrDays[$strWeekClass][$i]['addTitle'] = $GLOBALS['TL_LANG']['MSC']['caledit_addTitle'];
			
			// Inactive days
			if (empty($intKey) || !isset($arrAllEvents[$intKey]))
			{
				$arrDays[$strWeekClass][$i]['label'] = $intDay;
				$arrDays[$strWeekClass][$i]['class'] = 'days' . $strClass;
				if ($AddingAllowed) {
					$ts = mktime(8, 0, 0, $intMonth, $intDay, $intYear); // 8:00 at this day
					$arrDays[$strWeekClass][$i]['addref'] = $addUrl.'?add='.$this->parseDate($dateformat, $ts);
				}
				$arrDays[$strWeekClass][$i]['events'] = array();

				continue;
			}
			
			
			
			$arrEvents = array();

			// Get all events of a day
			foreach ($arrAllEvents[$intKey] as $v) {
				foreach ($v as $vv) {
					$arrEvents[] = $vv;
				}
			}

			$arrDays[$strWeekClass][$i]['label'] = $intDay;	
			if ($AddingAllowed) {
				$ts = mktime(8, 0, 0, $intMonth, $intDay, $intYear); // 8:00 at this day
				$arrDays[$strWeekClass][$i]['addref'] = $addUrl.'?add='.$this->parseDate($dateformat, $ts);				
			}
			$arrDays[$strWeekClass][$i]['class'] = 'days active' . $strClass;
			$arrDays[$strWeekClass][$i]['href'] = $this->strLink . ($GLOBALS['TL_CONFIG']['disableAlias'] ? '&amp;' : '?') . 'day=' . $intKey;
			$arrDays[$strWeekClass][$i]['title'] = sprintf(specialchars($GLOBALS['TL_LANG']['MSC']['cal_events']), count($arrEvents));
			$arrDays[$strWeekClass][$i]['events'] = $arrEvents;
		}

		return $arrDays;
	}
	
	public function generate()
    {
        if (TL_MODE == 'BE') {
            $objTemplate = new BackendTemplate('be_wildcard');

            $objTemplate->wildcard = '### CALENDER WITH FE EDITING ###';
            $objTemplate->title = $this->headline;
            $objTemplate->id = $this->id;
            $objTemplate->link = $this->name;
            $objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

            return $objTemplate->parse();
        }       

        return parent::generate();
    }


	/**
	 * Generate module
	 */
	protected function compile()
	{
        parent::compile();         	
	}
}
