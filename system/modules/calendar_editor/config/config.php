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
 * Front end modules
 */

$GLOBALS['FE_MOD']['events']['calendarEdit'] = 'ModuleCalenderEdit';
$GLOBALS['FE_MOD']['events']['EventEditor'] = 'ModuleEventEditor';
$GLOBALS['FE_MOD']['events']['EventReaderEditLink'] = 'ModuleEventReaderEdit';
$GLOBALS['FE_MOD']['events']['EventHiddenList'] = 'ModuleHiddenEventlist';

/**
 * Register hooks (merged with calendar_editor_plus)
 */

$GLOBALS['TL_HOOKS']['getAllEvents'][] = array('EventEditor', 'allEventsHook');
$GLOBALS['TL_HOOKS']['buildCalendarEditForm']['Edit'][] = array('EventEditHook', 'addFields'); 
$GLOBALS['TL_HOOKS']['prepareCalendarEditData']['Edit'][] = array('EventEditHook', 'prepareData');
