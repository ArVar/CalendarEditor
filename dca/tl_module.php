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

 $GLOBALS['TL_DCA']['tl_module']['palettes']['calendarEdit']        =  $GLOBALS['TL_DCA']['tl_module']['palettes']['calendar'].';{edit_legend},caledit_add_jumpTo'; // .'{expert_legend:hide},guests,cssID,space';
 $GLOBALS['TL_DCA']['tl_module']['palettes']['EventEditor']         = '{title_legend},name,headline,type;{redirect_legend},jumpTo;{config_legend},cal_calendar,caledit_mandatoryfields,caledit_allowPublish,caledit_sendMail;{template_legend}, caledit_template, caledit_tinMCEtemplate, caledit_alternateCSSLabel,caledit_usePredefinedCss;{protected_legend:hide},protected;{expert_legend:hide},guests,cssID,space';
 $GLOBALS['TL_DCA']['tl_module']['palettes']['EventReaderEditLink'] = '{title_legend},name,headline,type;{config_legend},cal_calendar';
 $GLOBALS['TL_DCA']['tl_module']['palettes']['EventHiddenList']     = $GLOBALS['TL_DCA']['tl_module']['palettes']['eventlist'];
  

 $GLOBALS['TL_DCA']['tl_module']['subpalettes']['caledit_usePredefinedCss'] = 'caledit_cssValues';
 $GLOBALS['TL_DCA']['tl_module']['subpalettes']['caledit_sendMail']         = 'caledit_mailRecipient';

 $GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'caledit_usePredefinedCss';
 $GLOBALS['TL_DCA']['tl_module']['palettes']['__selector__'][] = 'caledit_sendMail';



$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_allowPublish'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_allowPublish'],
	'inputType'               => 'checkbox',
	'sql'					  => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_sendMail'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_sendMail'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'w50'),
	'sql'					  => "char(1) NOT NULL default ''"
);
$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_mailRecipient'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_mailRecipient'],
	'inputType'               => 'text',
	'eval'                    => array('rgxp'=>'email', 'maxlength'=>255, 'tl_class'=>'w50'),
	'sql'					  => "varchar(255) NOT NULL default ''"
);


$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_mandatoryfields'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_mandatoryfields'],
	'inputType'               => 'checkbox',
    'options'                 => array('starttime','teaser', 'details', 'css'),
    'reference'               => &$GLOBALS['TL_LANG']['tl_caledit_mandatoryfields'],
	'eval'                    => array('multiple'=>true, 'tl_class'=>'w100'),
	'sql'					  => "blob NULL"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_add_jumpTo'] = array
(
 	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_add_jumpTo'],
 	'inputType'               => 'pageTree',
 	'eval'                    => array('fieldType'=>'radio'),
	'sql'					  => "int(10) unsigned NOT NULL default '0'"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_template'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_template'],
	'default'                 => 'eventedit_default',
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_eventeditor', 'getEventEditTemplates'),
	'eval'                    => array ('tl_class'=>'w50'),
	'sql'					  => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_tinMCEtemplate'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_tinMCEtemplate'],
	'default'                 => 'tinyFrontendMinimal',
	'inputType'               => 'select',
	'options_callback'        => array('tl_module_eventeditor', 'getConfigFiles'),
	'eval'			  		  => array('includeBlankOption'=>true),
	'sql'					  => "varchar(32) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_alternateCSSLabel'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_alternateCSSLabel'],
	'inputType'               => 'text',
	'eval'                    => array('maxlength'=>64, 'tl_class'=>'w100'),
	'sql'					  => "varchar(64) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_usePredefinedCss'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_usePredefinedCss'],
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true),
	'sql'					  => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_module']['fields']['caledit_cssValues'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_module']['caledit_cssValues'],
	'inputType'               => 'multitextWizard',	
	'eval'                    => array
      (
        'style'=>'width:100%;',
        'columns' => array
          (
            array
            (
              'name' => 'label', 
              'label' => &$GLOBALS['TL_LANG']['tl_module']['css_label'],
              'mandatory' => true,
              'width' => '100px' 
            ),
            array
            (
              'name' => 'value', 
              'label' => &$GLOBALS['TL_LANG']['tl_module']['css_value'],
              'mandatory' => true,
              'width' => '50px',
              'rgxp' => 'alpha', 
            )
          )
       ),
	'sql'					  => "blob NULL"
);


class tl_module_eventeditor extends Backend
{

	/**
	 * Return all event templates as array
	 * @param object
	 * @return array
	 */
	public function getEventEditTemplates(DataContainer $dc)
	{
		return $this->getTemplateGroup('eventEdit_', $dc->activeRecord->pid);
	}

    /**
     * Return a list of tinyMCE config files in this system.
     * copied from "FormRTE", @copyright  Andreas Schempp 2009
     */
    public function getConfigFiles()
	{
		$arrConfigs = array();
		$arrFiles = scan(TL_ROOT . '/system/config/');

		foreach( $arrFiles as $file ) {
			if (substr($file, 0, 4) == 'tiny') {
				$arrConfigs[] = basename($file, '.php');
			}
		}
		return $arrConfigs;
	}
}
