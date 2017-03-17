<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */


// Callbacks
$GLOBALS['TL_DCA']['tl_calendar']['config']['onsubmit_callback'][] = array('Markocupic\CKalenderXmlEventImport', 'updateCalendarsFromXMLOnSubmit');

// Palettes
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] = $GLOBALS['TL_DCA']['tl_calendar']['palettes']['default'] . ';{ckal_legend:hide},ckal_source';
$GLOBALS['TL_DCA']['tl_calendar']['palettes']['__selector__'][] = 'ckal_source';
$GLOBALS['TL_DCA']['tl_calendar']['subpalettes']['ckal_source'] = 'ckal_url,ckal_cache';


// Fields
$GLOBALS['TL_DCA']['tl_calendar']['fields']['ckal_source'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['ckal_source'],
	'exclude'                 => true,
	'filter'                  => true,
	'inputType'               => 'checkbox',
	'eval'                    => array('submitOnChange'=>true, 'tl_class'=>'clr m12'),
	'sql'                     => "char(1) NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['ckal_url'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['ckal_url'],
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'explanation' 			  => 'ckal_url',
	'eval'                    => array('tl_class'=>'long', 'helpwizard' => true),
	'sql'                     => "text NULL"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['ckal_cache'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['ckal_cache'],
	'default'                 => 86400,
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp' => 'digit', 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '86400'"
);

$GLOBALS['TL_DCA']['tl_calendar']['fields']['ckal_last_reload'] = array
(
	'label'                   => &$GLOBALS['TL_LANG']['tl_calendar']['ckal_last_reload'],
	'default'                 => 0,
	'exclude'                 => true,
	'search'                  => true,
	'inputType'               => 'text',
	'eval'                    => array('rgxp' => 'digit', 'tl_class'=>'w50'),
	'sql'                     => "int(10) unsigned NOT NULL default '0'"
);

