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
 * Class ModuleHiddenEventlist
 */
class ModuleHiddenEventlist extends ModuleEventlist
{

	/**
	 * Current date object
	 * @var integer
	 */
	protected $Date;

	/**
	 * Template
	 * @var string
	 */
	protected $strTemplate = 'mod_eventlist';
	
	/**
	 * GetAllEvents: Almost the same as in class Events, but with "published=0"
	 */
	protected function getAllEvents($arrCalendars, $intStart, $intEnd)
	{
		if (!is_array($arrCalendars))
		{
			return array();
		}

		$this->import('String');

		$time = time();
		$this->arrEvents = array();

		foreach ($arrCalendars as $id)
		{
			$strUrl = $this->strUrl;

			// Get current "jumpTo" page
			$objPage = $this->Database->prepare("SELECT id, alias FROM tl_page WHERE id=(SELECT jumpTo FROM tl_calendar WHERE id=?)")
									  ->limit(1)
									  ->execute($id);

			if ($objPage->numRows) {
				$strUrl = $this->generateFrontendUrl($objPage->row(), '/events/%s');
			}

			// Get events of the current period
			$objEvents = $this->Database->prepare("SELECT *, (SELECT title FROM tl_calendar WHERE id=?) AS calendar, (SELECT name FROM tl_user WHERE id=author) author FROM tl_calendar_events WHERE pid=? AND ((startTime>=? AND startTime<=?) OR (endTime>=? AND endTime<=?) OR (startTime<=? AND endTime>=?) OR (recurring=1 AND (recurrences=0 OR repeatEnd>=?) AND startTime<=?))" . (!BE_USER_LOGGED_IN ? " AND (start='' OR start<$time) AND (stop='' OR stop>$time) AND published=0" : "") . " ORDER BY startTime")
										->execute($id, $id, $intStart, $intEnd, $intStart, $intEnd, $intStart, $intEnd, $intStart, $intEnd);

			if ($objEvents->numRows < 1) {
				continue;
			}

			while ($objEvents->next()) {
				$this->addEvent($objEvents, $objEvents->startTime, $objEvents->endTime, $strUrl, $intStart, $intEnd, $id);

				// Recurring events
				if ($objEvents->recurring) {
					$count = 0;
					$arrRepeat = deserialize($objEvents->repeatEach);

					while ($objEvents->endTime < $intEnd) {
						if ($objEvents->recurrences > 0 && $count++ >= $objEvents->recurrences) {
							break;
						}

						$arg = $arrRepeat['value'];
						$unit = $arrRepeat['unit'];

						if ($arg < 1) {
							break;
						}

						$strtotime = '+ ' . $arg . ' ' . $unit;

						$objEvents->startTime = strtotime($strtotime, $objEvents->startTime);
						$objEvents->endTime = strtotime($strtotime, $objEvents->endTime);

						// Skip events outside the scope
						if ($objEvents->endTime < $intStart || $objEvents->startTime > $intEnd) {
							continue;
						}

						$this->addEvent($objEvents, $objEvents->startTime, $objEvents->endTime, $strUrl, $intStart, $intEnd, $id);
					}
				}
			}
		}

		// Sort data
		foreach (array_keys($this->arrEvents) as $key)
		{
			ksort($this->arrEvents[$key]);
		}

		// HOOK: modify result set
		if (isset($GLOBALS['TL_HOOKS']['getAllEvents']) && is_array($GLOBALS['TL_HOOKS']['getAllEvents']))
		{
			foreach ($GLOBALS['TL_HOOKS']['getAllEvents'] as $callback)
			{
				$this->import($callback[0]);
				$this->arrEvents = $this->$callback[0]->$callback[1]($this->arrEvents, $arrCalendars, $intStart, $intEnd, $this);
			}
		}		

		return $this->arrEvents;
	}


	/**
	 * Display a wildcard in the back end
	 * @return string
	 */
	public function generate()
	{
		if (TL_MODE == 'BE')
		{
			$objTemplate = new BackendTemplate('be_wildcard');

			$objTemplate->wildcard = '### UNPULISHED EVENT LIST ###';
			$objTemplate->title = $this->headline;
			$objTemplate->id = $this->id;
			$objTemplate->link = $this->name;
			$objTemplate->href = 'contao/main.php?do=themes&amp;table=tl_module&amp;act=edit&amp;id=' . $this->id;

			return $objTemplate->parse();
		}

		$this->cal_calendar = $this->sortOutProtected(deserialize($this->cal_calendar, true));

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
		parent::compile();
	}
}
