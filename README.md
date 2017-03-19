# ckalender_xml_event_import
Diese Modul ermöglicht die Synchronisierung von Events auf ckalender.de nach tl_calendar_events im CMS Contao.

In den Kalendereinstellungen muss die Synchronisation aktiviert werden. Dabei muss eine url zur ckalender.de XML-Schnittstelle angegeben werden.
Die URL-Parameter „VonDatumD“ und „BisDatumD“ können mit dem Platzhalter ###+*days### oder ###-*days### dynamisch hinterlegt werden. Wobei * für die Anzahl Tage ab heute steht.
Mit dem Aktualisierungsintervall kann gesteurt werden, in welchen Abständen der Synchronisierungsmechanismus ausgelöst werden soll.
Wird eine Synchronisierung ausgelöst, werden zunächst alle Events des Kalenders auf unsichtbar gestellt. Danach werden die Contao Events mit den ckalender Events überschrieben und wieder auf sichtbar gestellt. Die Contao-events, die in der XML-Datei nicht enthalten sind, bleiben auf unsichtbar. Die ckalender event-id entspricht dabei der tl_calendar_events.uuid.

Der Update-Prozess kann zusätzlich mit dem CKalenderXMLEventImportBeforeUpdateHook-beeinflusst werden. Die Methode erwartet 4 Parameter ($arrSet, $child, $xml, $xmlString) und gibt als Rückgabewert $arrSet zurück.
