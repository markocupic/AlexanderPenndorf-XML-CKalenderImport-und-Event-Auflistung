# ckalender_xml_event_import
Diese Modul erm�glicht die Synchronisierung von Events auf ckalender.de nach tl_calendar_events im CMS Contao.

In den Kalendereinstellungen muss die Synchronisation aktiviert werden. Dabei muss eine url zur ckalender.de XML-Schnittstelle angegeben werden.
Die URL-Parameter �VonDatumD� und �BisDatumD� k�nnen mit dem Platzhalter ###+*days### oder ###-*days### dynamisch hinterlegt werden. Wobei * f�r die Anzahl Tage ab heute steht.
Mit dem Aktualisierungsintervall kann gesteurt werden, in welchen Abst�nden der Synchronisierungsmechanismus ausgel�st werden soll.
Wird eine Synchronisierung ausgel�st, werden zun�chst alle Events des Kalenders auf unsichtbar gestellt. Danach werden die Contao Events mit den ckalender Events �berschrieben und wieder auf sichtbar gestellt. Die Contao-events, die in der XML-Datei nicht enthalten sind, bleiben auf unsichtbar. Die ckalender event-id entspricht dabei der tl_calendar_events.uuid.

Der Update-Prozess kann zus�tzlich mit dem CKalenderXMLEventImportBeforeUpdateHook-beeinflusst werden. Die Methode erwartet 4 Parameter ($arrSet, $child, $xml, $xmlString) und gibt als R�ckgabewert $arrSet zur�ck.
