<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */


$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = str_replace('teaser;', 'teaser;{ckal_fields},uuid,notiz,verantwortlich,benutzergruppe,text;', $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']);


// Keys
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['sql']['keys']['uuid'] = 'unique';

// Fields
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['uuid'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['uuid'],
    'search'    => true,
    'exclude'   => true,
    'default'   => Markocupic\CKalenderXmlEventImport::generateUuid(),
    'inputType' => 'text',
    'eval'      => array('mandatory' => true, 'readonly' => true, 'doNotCopy' => true, 'rgxp' => 'digit'),
    'sql'       => "varchar(128) NOT NULL default ''",
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['notiz'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['notiz'],
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'textarea',
    'eval'      => array('tl_class' => 'clr'),
    'sql'       => "text NULL",
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['verantwortlich'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['verantwortlich'],
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'textarea',
    'eval'      => array('tl_class' => 'clr'),
    'sql'       => "text NULL",
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['benutzergruppe'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['benutzergruppe'],
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'textarea',
    'eval'      => array('tl_class' => 'clr'),
    'sql'       => "text NULL",
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['text'] = array(
    'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events']['text'],
    'exclude'   => true,
    'search'    => true,
    'inputType' => 'textarea',
    'eval'      => array('tl_class' => 'clr'),
    'sql'       => "text NULL",
);