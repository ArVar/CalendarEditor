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
 * Add palettes to tl_module
 */

$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default']. ';{edit_legend},AllowEdit';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['AllowEdit']='caledit_jumpTo, caledit_groups,caledit_onlyUser,caledit_adminGroup';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'AllowEdit';

$GLOBALS['TL_DCA']['tl_calendar']['fields']['AllowEdit'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['AllowEdit'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'					  =>"char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_jumpTo'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_jumpTo'],
	'exclude'                 => true,
	'inputType'               => 'pageTree',
	'eval'                    => array('fieldType'=>'radio'),
	'sql'					  => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_groups'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_groups'],
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_member_group.name',
    'eval'                    => array('mandatory'=>true, 'multiple'=>true),
	'sql'					  => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_adminGroup'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_adminGroup'],
	'inputType'               => 'checkbox',
	'foreignKey'              => 'tl_member_group.name',
	'eval'                    => array('mandatory'=>false, 'multiple'=>true),
	'sql'					  => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['caledit_onlyUser'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['caledit_onlyUser'],
	'exclude'                 => true,
	'inputType'               => 'checkbox',
	'sql'					  => "char(1) NOT NULL default ''"
);
