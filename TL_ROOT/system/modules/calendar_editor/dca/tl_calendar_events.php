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

$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']. ';{edit_legend},FE_User, disable_editing';


$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['FE_User'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['FE_User'],
	'inputType'               => 'select',
	'exclude'                 => true,
	'foreignKey'              => 'tl_member.username',
	'eval'                    => array('includeBlankOption'=>true, 'tl_class'=>'w50'),
	'sql'					  => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['disable_editing'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar_events']['disable_editing'],
	'inputType'               => 'checkbox',
    'eval'                    => array('tl_class'=>'w50'),
	'sql'					  => "char(1) NOT NULL default ''"
);
