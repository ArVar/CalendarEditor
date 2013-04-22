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
 
$GLOBALS['TL_LANG']['tl_module']['caledit_add_jumpTo']     = array('Redirect page for adding events', 'Please choose the page to which the FE user will be redirected when adding an event.');
$GLOBALS['TL_LANG']['tl_module']['caledit_template']       = array('Event editor template', 'Here you can select the template for the event editor.');
$GLOBALS['TL_LANG']['tl_module']['caledit_tinMCEtemplate'] = array('Rich text editor', 'Select a configuration file if you want to use TinyMCE rich text editor. You can create custom configurations by adding a file called tinyXXX in system/config.');

$GLOBALS['TL_LANG']['tl_module']['caledit_allowPublish']   = array('Allow publishing','If this is checked, the FE user is allowed to publish/hide events. Otherwise a BE user has to publish it.');
$GLOBALS['TL_LANG']['tl_module']['caledit_sendMail']       = array('Send email','Send a notification email when a new event is created.');
$GLOBALS['TL_LANG']['tl_module']['caledit_mailRecipient']  = array('Mail recipient','Enter the email address the notification should be sent to.');

$GLOBALS['TL_LANG']['tl_module']['caledit_alternateCSSLabel'] = array('Label for field "CSS"','Enter an alternate label for the CSS field (e.g. "location" or "trainer")');
$GLOBALS['TL_LANG']['tl_module']['caledit_mandatoryfields']   = array('Additional mandatory fields','Select additional mandatory fields. Note: "Startdate" and "Title" are ALWAYS mandatory.');

$GLOBALS['TL_LANG']['tl_module']['caledit_usePredefinedCss'] = array('Use predefined CSS classes','Define some CSS-classes the FE user can choose.');
$GLOBALS['TL_LANG']['tl_module']['caledit_cssValues'] = array('CSS label/values', 'Enter a list of some label/values for CSS. The "label" is shown in the selection, the "value" is used as CSS class in the event.');


$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields']['starttime'] = 'Start time';
$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields']['teaser']    = 'Teaser';
$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields']['details']   = 'Details';
$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields']['css']       = 'CSS class';


$GLOBALS['TL_LANG']['tl_module']['edit_legend'] = 'Frontend editing';
$GLOBALS['TL_LANG']['tl_module']['css_label'] = 'Label';
$GLOBALS['TL_LANG']['tl_module']['css_value'] = 'Value';
