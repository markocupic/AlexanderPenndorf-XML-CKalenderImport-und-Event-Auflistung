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
    'config' => array(
        'dataContainer' => 'Table',
        'ptable' => 'tl_calendar_events',
        'enableVersioning' => true,
        'sql' => array(
            'keys' => array(
                'id' => 'primary',
                'pid,memberId' => 'index',
            ),
        ),
    ),
    // List
    'list' => array(
        'sorting' => array(
            'mode' => 1,
            'fields' => array('pid'),
            'disableGrouping' => true,
            'flag' => 1,
        ),
        'label' => array(
            'fields' => array('memberId'),
            'label_callback' => array('tl_calendar_events_rating', 'childRecordCallback')
        ),
        'global_operations' => array(
            'all' => array(
                'label' => &$GLOBALS['TL_LANG']['MSC']['all'],
                'href' => 'act=select',
                'class' => 'header_edit_all',
                'attributes' => 'onclick="Backend.getScrollOffset();" accesskey="e"',
            ),
        ),
        'operations' => array(
            'edit' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['edit'],
                'href' => 'act=edit',
                'icon' => 'edit.gif',
            ),
            'copy' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['copy'],
                'href' => 'act=copy',
                'icon' => 'copy.gif',
            ),
            'delete' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['delete'],
                'href' => 'act=delete',
                'icon' => 'delete.gif',
                'attributes' => 'onclick="if(!confirm(\'' . $GLOBALS['TL_LANG']['MSC']['deleteConfirm'] . '\'))return false;Backend.getScrollOffset()"',
            ),
            'show' => array(
                'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['show'],
                'href' => 'act=show',
                'icon' => 'show.gif',
            ),
        ),
    ),
    // Select
    'select' => array(
        'buttons_callback' => array(),
    ),
    // Edit
    'edit' => array(
        'buttons_callback' => array(),
    ),
    // Palettes
    'palettes' => array(
        '__selector__' => array(''),
        'default' => '{author_legend},memberId;{rating_legend},' . implode(',', $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['ratings']) . ';{vertraege_legend},' . implode(',', $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['vertraege']) . ';{hardware_legend},' . implode(',', $GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['hardware']) . ';{info_legend},weitereInfo;',
    ),
    // Subpalettes
    'subpalettes' => array(
        '' => '',
    ),
    // Fields
    'fields' => array(
        'id' => array(
            'sql' => "int(10) unsigned NOT NULL auto_increment",
        ),
        'tstamp' => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'pid' => array(
            'sql' => "int(10) unsigned NOT NULL default '0'",
        ),
        'memberId' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['memberId'],
            //'default'                 => BackendUser::getInstance()->id,
            'exclude' => true,
            'inputType' => 'select',
            'foreignKey' => 'tl_member.CONCAT(firstname," ",lastname)',
            'eval' => array('mandatory' => true, 'chosen' => true, 'doNotCopy' => true, 'includeBlankOption' => true),
            'sql' => "int(10) unsigned NOT NULL default '0'",
            'relation' => array('type' => 'belongsTo', 'load' => 'eager'),
        ),
        'weitereInfo' => array(
            'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating']['weitereInfo'],
            'exclude' => true,
            'inputType' => 'textarea',
            'eval' => array('mandatory' => true),
            'sql' => "mediumtext NULL",
        )
    ),
);

// Add fields to tl_calendar_events_rating the fieldnames are defined for more flexible usage in config.php
foreach ($GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['ratings'] as $field)
{
    $GLOBALS['TL_DCA']['tl_calendar_events_rating']['fields'][$field] = array(

        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating'][$field],
        'exclude' => true,
        'inputType' => 'select',
        'options' => range(0, 6),
        'eval' => array('mandatory' => true, 'maxlength' => 1, 'rgxp' => 'natural'),
        'sql' => "int(1) unsigned NOT NULL default '0'",
    );
}

// Add fields to tl_calendar_events_rating the fieldnames are defined for more flexible usage in config.php
foreach ($GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['vertraege'] as $field)
{
    $GLOBALS['TL_DCA']['tl_calendar_events_rating']['fields'][$field] = array(
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating'][$field],
        'default' => 0,
        'exclude' => true,
        'inputType' => 'text',
        'eval' => array('mandatory' => true, 'maxlength' => 12, 'rgxp' => 'natural'),
        'sql' => "int(12) unsigned NOT NULL default '0'",
    );
}

// Add fields to tl_calendar_events_rating the fieldnames are defined for more flexible usage in config.php
foreach ($GLOBALS['TL_CONFIG']['CUSTOM_EVENTS']['hardware'] as $field)
{
    $GLOBALS['TL_DCA']['tl_calendar_events_rating']['fields'][$field] = array(
        'label' => &$GLOBALS['TL_LANG']['tl_calendar_events_rating'][$field],
        'default' => 0,
        'exclude' => true,
        'inputType' => 'text',
        'eval' => array('mandatory' => true, 'maxlength' => 12, 'rgxp' => 'natural'),
        'sql' => "int(12) unsigned NOT NULL default '0'",
    );
}


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class tl_calendar_events_rating extends Backend
{

    /**
     * @param $row
     * @return string
     */
    public function childRecordCallback($row)
    {
        $name = 'no name';
        $objMember = \MemberModel::findByPk($row['memberId']);
        if ($objMember !== null)
        {
            $name = $objMember->firstname . ' ' . $objMember->lastname;
        }

        return '<div style="float:left">Bewertung von: ' . $name . "</div>\n";
    }
}



