<?php

/**
 * Created by PhpStorm.
 * User: Marko Cupic m.cupic@gmx.ch
 * Date: 16.03.2017
 * Time: 12:55
 */

// config.php
if(TL_MODE == 'FE')
{
    $GLOBALS['TL_HOOKS']['generatePage'][] = array('Markocupic\CKalenderXmlEventImport', 'updateCalendarsFromXML');
}

