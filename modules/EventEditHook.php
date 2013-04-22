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
class EventEditHook extends Frontend
{

	public function addFields($NewEventData, $fields, $currentEventObject, $editID) {
		$result = array();
		$result['NewEventData'] = $NewEventData; 
		$result['fields']       = $fields;
		
		// Get Calendar_Events - Data from current event
		if ($editID) {
			$result['NewEventData']['cep_location']     = $currentEventObject->cep_location;
			$result['NewEventData']['cep_participants'] = $currentEventObject->cep_participants;
			$result['NewEventData']['cep_contact']      = $currentEventObject->cep_contact;			
		}
		// overwrite it with current POST data
		if ($this->Input->post('FORM_SUBMIT') == 'caledit_submit') {
			$result['NewEventData']['cep_location']     = $this->Input->post('cep_location');
			$result['NewEventData']['cep_participants'] = $this->Input->post('cep_participants');
			$result['NewEventData']['cep_contact']      = $this->Input->post('cep_contact');
		}
		
		// create new fields		
		$result['fields']['cep_location'] = array(
				'name' => 'cep_location',
				'label' => $GLOBALS['TL_LANG']['MSC']['cep_location'],
				'inputType' => 'text',
				'value' => $result['NewEventData']['cep_location'],
				'eval' => array('maxlength' => 255)
				);
		$result['fields']['cep_participants'] = array(
				'name' => 'cep_participants',
				'label' => $GLOBALS['TL_LANG']['MSC']['cep_participants'],
				'inputType' => 'text',
				'value' => $result['NewEventData']['cep_participants'],
				'eval' => array('maxlength' => 255)
				);
		$result['fields']['cep_contact'] = array(
				'name' => 'cep_contact',
				'label' => $GLOBALS['TL_LANG']['MSC']['cep_contact'],
				'inputType' => 'text',
				'value' => $result['NewEventData']['cep_contact'],
				'eval' => array('maxlength' => 255)
				);	
					
		return $result;
	}	
	
	public function prepareData($eventData) {
		$result = $eventData;
		$addDet = "<p>Location: ".$eventData['cep_location']."</p>";
		$addDet.= "<p>Participiants: ".$eventData['cep_participants']."</p>";
		$addDet.= "<p>Contact: ".$eventData['cep_contact']."</p>";
		// Add location, participiants and contact to details
		$result['details'] = $addDet.$result['details'];
		// delete these fieldes from the data-array, as these columns does not exist in tl_calendar_events
		// (they actually do, if you have installed the extension calendar_events_plus)
		unset($result['cep_location']);
		unset($result['cep_participants']);
		unset($result['cep_contact']);
		return $result;
		
	}
}
*/