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
 * Class EventEditor
 */
/*
class EventEditHook extends \Frontend
{

	public function addFields($NewEventData, $fields, $currentEventObject, $editID) {
		$result = array();
		$result['NewEventData'] = $NewEventData; 
		$result['fields']       = $fields;
		
		// Get Calendar_Events - Data from current event
		if ($editID) {
			$result['NewEventData']['ce_location']     = $currentEventObject->ce_location;
			$result['NewEventData']['ce_participants'] = $currentEventObject->ce_participants;
			$result['NewEventData']['ce_contact']      = $currentEventObject->ce_contact;			
		}
		// overwrite it with current POST data
		if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
			$result['NewEventData']['ce_location']     = $this->Input->post('ce_location');
			$result['NewEventData']['ce_participants'] = $this->Input->post('ce_participants');
			$result['NewEventData']['ce_contact']      = $this->Input->post('ce_contact');
		}
		
		// create new fields		
		$result['fields']['ce_location'] = array(
				'name' => 'ce_location',
				'label' => $GLOBALS['TL_LANG']['MSC']['ce_location'],
				'inputType' => 'text',
				'value' => $result['NewEventData']['ce_location'],
				'eval' => array('maxlength' => 255)
				);
		$result['fields']['ce_participants'] = array(
				'name' => 'ce_participants',
				'label' => $GLOBALS['TL_LANG']['MSC']['ce_participants'],
				'inputType' => 'text',
				'value' => $result['NewEventData']['ce_participants'],
				'eval' => array('maxlength' => 255)
				);
		$result['fields']['ce_contact'] = array(
				'name' => 'ce_contact',
				'label' => $GLOBALS['TL_LANG']['MSC']['ce_contact'],
				'inputType' => 'text',
				'value' => $result['NewEventData']['ce_contact'],
				'eval' => array('maxlength' => 255)
				);	
					
		return $result;
	}	
	
	public function prepareData($eventData) {
		$result = $eventData;
		$addDet = "<p>Location: ".$eventData['ce_location']."</p>";
		$addDet.= "<p>Participiants: ".$eventData['ce_participants']."</p>";
		$addDet.= "<p>Contact: ".$eventData['ce_contact']."</p>";
		// Add location, participiants and contact to details
		$result['details'] = $addDet.$result['details'];
		// delete these fieldes from the data-array, as these columns does not exist in tl_calendar_events
		// (they actually do, if you have installed the extension calendar_events_plus)
		unset($result['ce_location']);
		unset($result['ce_participants']);
		unset($result['ce_contact']);
		return $result;
		
	}
}
*/