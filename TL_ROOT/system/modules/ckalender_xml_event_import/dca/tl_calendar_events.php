<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */


$GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default'] = str_replace('teaser;',
    'teaser;{ckal_fields},uuid,notiz,verantwortlich,benutzergruppe,text;',
    $GLOBALS['TL_DCA']['tl_calendar_events']['palettes']['default']);


// Keys
$GLOBALS['TL_DCA']['tl_calendar_events']['config']['sql']['keys']['uuid'] = 'index';

// Fields
$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['uuid'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['uuid'],
    'search' => true,
    'exclude' => true,
    'inputType' => 'text',
    'eval' => array(
        //'mandatory' => true,
        'doNotShow' => true,
        'rgxp' => 'digit',
    ),
    'sql' => "varchar(128) COLLATE utf8_bin NOT NULL default ''"
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['notiz'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['notiz'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => array('tl_class' => 'clr'),
    'sql' => "text NULL",
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['verantwortlich'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['verantwortlich'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => array('tl_class' => 'clr'),
    'sql' => "text NULL",
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['benutzergruppe'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['benutzergruppe'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => array('tl_class' => 'clr'),
    'sql' => "text NULL",
);

$GLOBALS['TL_DCA']['tl_calendar_events']['fields']['text'] = array(
    'label' => &$GLOBALS['TL_LANG']['tl_calendar_events']['text'],
    'exclude' => true,
    'search' => true,
    'inputType' => 'textarea',
    'eval' => array('tl_class' => 'clr'),
    'sql' => "text NULL",
);


/**
 * Provide miscellaneous methods that are used by the data configuration array.
 *
 * @author Leo Feyer <https://github.com/leofeyer>
 */
class tl_ckalendar_calendar_events extends Backend
{

    /**
     * Import the back end user object
     */
    public function __construct()
    {
        parent::__construct();
        $this->import('Session');
    }

    /**
     * @param string $varValue
     * @return int|string
     */
    public function generateUuid($varValue = '')
    {
        //throw new \Exception();

        if (\Input::get('table') != 'tl_calendar_events' && \Input::get('do') != 'calendar')
        {
            return $varValue;
        }


        $objDb = \Database::getInstance();
        if (!$objDb->fieldExists('uuid', 'tl_calendar_events'))
        {
            return $varValue;
        }

        if (!isset($_SESSION['CKALENDAR']['uuid']))
        {
            $_SESSION['CKALENDAR']['uuid'] = array();
        }


        // Generate uuid
        $uuid = 1000000000;
        $skip = false;
        while ($skip === false)
        {
            $uuid++;
            $objCal = \Database::getInstance()->prepare('SELECT * FROM tl_calendar_events WHERE uuid=?')->execute($uuid);
            if ($objCal->numRows < 1 && !in_array($uuid, $_SESSION['CKALENDAR']['uuid']))
            {
                $skip = true;
            }
            $_SESSION['CKALENDAR']['uuid'][] = $uuid;
        }
        return $uuid;
    }

}