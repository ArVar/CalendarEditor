<?php

/**
 * Contao Open Source CMS
 * 
 * Copyright (C) 2005-2013 Leo Feyer
 * 
 * @package Calendar_editor
 * @link    https://contao.org
 * @license http://www.gnu.org/licenses/lgpl-3.0.html LGPL
 */


/**
 * Register the classes
 */
ClassLoader::addClasses(array
(
	'ModuleHiddenEventlist' => 'system/modules/calendar_editor/modules/ModuleHiddenEventlist.php',
	'EventEditor'           => 'system/modules/calendar_editor/modules/EventEditor.php',
	'ModuleEventEditor'     => 'system/modules/calendar_editor/modules/ModuleEventEditor.php',
	'ModuleEventReaderEdit' => 'system/modules/calendar_editor/modules/ModuleEventReaderEdit.php',
	'ModuleCalenderEdit'    => 'system/modules/calendar_editor/modules/ModuleCalenderEdit.php',
	'EventEditHook'    		=> 'system/modules/calendar_editor/modules/EventEditHook.php',
));


/**
 * Register the templates
 */
TemplateLoader::addFiles(array
(
	'cal_default_edit'       		=> 'system/modules/calendar_editor/templates',
	'event_full_edit'        		=> 'system/modules/calendar_editor/templates',
	'event_list_edit'        		=> 'system/modules/calendar_editor/templates',
	'event_upcoming_edit'    		=> 'system/modules/calendar_editor/templates',
	'event_upcoming_unpublished'    => 'system/modules/calendar_editor/templates',
	'eventEdit_default'        		=> 'system/modules/calendar_editor/templates',
	'eventEdit_enhanced'        	=> 'system/modules/calendar_editor/templates',
	'mod_event_ReaderEditLink'		=> 'system/modules/calendar_editor/templates',
));