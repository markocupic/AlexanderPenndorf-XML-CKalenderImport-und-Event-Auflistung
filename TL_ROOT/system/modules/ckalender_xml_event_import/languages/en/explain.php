<?php

// Fields
$GLOBALS['TL_LANG']['tl_calendar']['ckal_source'] = array("CKalender Webdaten", "Einen Kalender aus einer CKalender XML-URL-Datenquelle generieren");
$GLOBALS['TL_LANG']['tl_calendar']['ckal_url'] = array("Kalender-URL", "z.B. https://www.ckalender.de/modul_termine_xml.php?.....");
$GLOBALS['TL_LANG']['tl_calendar']['ckal_cache'] = array("Cache-Dauer in Sekunden", "Bitte geben Sie die minimale Cache-Dauer in Sekunden für die Pufferung der CKalender-Daten an. Die Kalenderdaten werden erst nach dem Ablauf der Cache-Dauer wieder neu aufgebaut.");

// Legends
$GLOBALS['TL_LANG']['tl_calendar']['ckal_legend'] = "CKalender Einstellungen";

// Explanation
$GLOBALS['TL_LANG']['XPL']['ckal_url'] = '<h2>Zeitraum dynamisch setzen</h2><p>Mit den Variablen <strong>###+*days###</strong> oder <strong>###-*days###</strong> können die URL-Parameter VonDatumD und BisDatumD dynamisch gesetzt werden. Wobei * immer f&uuml;r die Anzahl Tage steht.</p><p>Beispielsweise:<br>https://www.ckalender.de/modul_termine_xml.php?OrgID=7253&OrgPW=ac0de270b2d6494233793b0beac455d0&VonDatumD=###-2days###&BisDatumD=###+30days###&Zyklisch=nein&Gruppen=*</p>';