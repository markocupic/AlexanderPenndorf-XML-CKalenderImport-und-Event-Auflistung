<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */


/**
 * Table tl_calendar_events_rating
 */
$GLOBALS['TL_DCA']['tl_calendar_events_rating'] = array(

    // Config
    'config'      => array(
        'dataContainer'    => 'Table',
        'ptable'           => 'tl_calendar_events',
        'enableVersioning' => true,
        'sql'              => array(
            'keys' => array(
                'id'           => 'primary',
                'pid,memberId' => 'index',
            ),
        ),
    ),
    // List
    'list'        => array(
        'sorting'           => array(
            'mode'   => 1,
            'fields' => array('pid'),
            'flag'   => 1,
        ),
        'label'             => array(
            'fields' => array(''),
            'format' => '%s',
        ),
        'global_operations' => array(
            'all' => array(
                'label'      => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href'       => 'act=select',
                'class'      => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ),
        ),
        'operations'        => array(
            'edit'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['edit'],
                'href'  => 'act=edit',
                'icon'  => 'edit.gif',
            ),
            'copy'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['copy'],
                'href'  => 'act=copy',
                'icon'  => 'copy.gif',
            ),
            'delete' => array(
                'label'      => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['delete'],
                'href'       => 'act=delete',
                'icon'       => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show'   => array(
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['show'],
                'href'  => 'act=show',
                'icon'  => 'show.gif',
            ),
        ),
    ),
    // Select
    'select'      => array(
        'buttons_callback' => array(),
    ),
    // Edit
    'edit'        => array(
        'buttons_callback' => array(),
    ),
    // Palettes
    'palettes'    => array(
        '__selector__' => array(''),
        'default'      => '{title_legend},memberId,ratingTechnikImShop,ratingAufbauAbbauVorOrt,ratingKundenfrequenzImShop,ratingZustandShop,ratingUnterstuetzungImShop,ratingWetter,anzahlDurchgefuehrteBeratungen,anzahlAbgeschlosseneVertraege,festnetz,kreditMobile,kommentarZumEinsatz;',
    ),
    // Subpalettes
    'subpalettes' => array(
        '' => '',
    ),
    // Fields
    'fields'      => array(
        'id'                             => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp'                         => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'pid'                            => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'memberId'                       => array(
            'label'      => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['memberId'],
            //'default'                 => BackendUser::getInstance()->id,
            'exclude'    => true,
            'inputType'  => 'select',
            'foreignKey' => 'tl_member.CONCAT(firstname," ",lastname)',
            'eval'       => array('mandatory' => true, 'chosen' => true, 'doNotCopy' => true, 'includeBlankOption' => true),
            'sql'        => "int(10) unsigned NOT NULL default '0'",
            'relation'   => array('type' => 'belongsTo', 'load' => 'eager'),
        ),
        'ratingTechnikImShop'            => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['ratingTechnikImShop'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => range(0, 6),
            'eval'      => array('mandatory' => true, 'maxlength' => 1, 'rgxp' => 'natural'),
            'sql'       => "int(1) unsigned NOT NULL default '0'",
        ),
        'ratingAufbauAbbauVorOrt'        => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['ratingAufbauAbbauVorOrt'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => range(0, 6),
            'eval'      => array('mandatory' => true, 'maxlength' => 1, 'rgxp' => 'natural'),
            'sql'       => "int(1) unsigned NOT NULL default '0'",
        ),
        'ratingKundenfrequenzImShop'     => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['ratingKundenfrequenzImShop'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => range(0, 6),
            'eval'      => array('mandatory' => true, 'maxlength' => 1, 'rgxp' => 'natural'),
            'sql'       => "int(1) unsigned NOT NULL default '0'",
        ),
        'ratingZustandShop'              => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['ratingZustandShop'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => range(0, 6),
            'eval'      => array('mandatory' => true, 'maxlength' => 1, 'rgxp' => 'natural'),
            'sql'       => "int(1) unsigned NOT NULL default '0'",
        ),
        'ratingUnterstuetzungImShop'     => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['ratingUnterstuetzungImShop'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => range(0, 6),
            'eval'      => array('mandatory' => true, 'maxlength' => 1, 'rgxp' => 'natural'),
            'sql'       => "int(1) unsigned NOT NULL default '0'",
        ),
        'ratingWetter'                   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['ratingWetter'],
            'exclude'   => true,
            'inputType' => 'select',
            'options'   => range(0, 6),
            'eval'      => array('mandatory' => true, 'maxlength' => 1, 'rgxp' => 'natural'),
            'sql'       => "int(1) unsigned NOT NULL default '0'",
        ),
        'anzahlDurchgefuehrteBeratungen' => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['anzahlDurchgefuehrteBeratungen'],
            'default'   => 0,
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 12, 'rgxp' => 'natural'),
            'sql'       => "int(12) unsigned NOT NULL default '0'",
        ),
        'anzahlAbgeschlosseneVertraege'  => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['anzahlAbgeschlosseneVertraege'],
            'default'   => 0,
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 12, 'rgxp' => 'natural'),
            'sql'       => "int(12) unsigned NOT NULL default '0'",
        ),
        'festnetz'                       => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['festnetz'],
            'default'   => 0,
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 12, 'rgxp' => 'natural'),
            'sql'       => "int(12) unsigned NOT NULL default '0'",
        ),
        'kreditMobile'                   => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['kreditMobile'],
            'default'   => 0,
            'exclude'   => true,
            'inputType' => 'text',
            'eval'      => array('mandatory' => true, 'maxlength' => 12, 'rgxp' => 'natural'),
            'sql'       => "int(12) unsigned NOT NULL default '0'",
        ),
        'kommentarZumEinsatz'            => array(
            'label'     => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['kommentarZumEinsatz'],
            'default'   => 0,
            'exclude'   => true,
            'inputType' => 'textarea',
            'eval'      => array('mandatory' => true, 'maxlength' => 255, 'rgxp' => 'number'),
            'sql'       => "mediumtext NULL",
        ),

    ),
);
