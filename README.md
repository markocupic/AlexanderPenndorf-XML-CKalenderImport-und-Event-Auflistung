# ckalender_xml_event_import
Dieses Modul ermöglicht die Synchronisierung von Events auf ckalender.de nach tl_calendar_events im CMS Contao.

In den Kalendereinstellungen muss die Synchronisation aktiviert werden. Dabei muss eine url zur ckalender.de XML-Schnittstelle angegeben werden.
Die URL-Parameter „VonDatumD“ und „BisDatumD“ können mit dem Platzhalter ###+*days### oder ###-*days### dynamisch hinterlegt werden. Wobei * für die Anzahl Tage ab heute steht.
Mit dem Aktualisierungsintervall kann gesteurt werden, in welchen Abständen der Synchronisierungsmechanismus ausgelöst werden soll.
Wird eine Synchronisierung ausgelöst, werden zunächst alle Events des Kalenders auf unsichtbar gestellt. Danach werden die Contao Events mit den ckalender Events überschrieben und wieder auf sichtbar gestellt. Die Contao-events, die in der XML-Datei nicht enthalten sind, bleiben auf unsichtbar. Die ckalender event-id entspricht dabei der tl_calendar_events.uuid.

Der Update-Prozess kann zusätzlich mit dem CKalenderXMLEventImportBeforeUpdateHook-beeinflusst werden. Die Methode erwartet 4 Parameter ($arrSet, $child, $xml, $xmlString) und gibt als Rückgabewert $arrSet zurück.


# custom-events
Dieses Modul erweteitert das Contao Kalender Modul um weitere Funktionalitäten.

### Bildupload in der Detailansicht des Events.
Angemeldete Benutzer können zum ausgewählten Event Bilder hochladen. Die Bilder werden nach dem Upload ins Verzeichnis files/events/event-id verschoben.
Das Verzeichnis kann in der config.php des Moduls angepasst werden.
Diese Bilder können dann mit dem dafür angefertigten Modul in der Detailansicht angezeigt werden. Bilder können direkt aus dem Frontend auch wieder gelöscht werden.

Bei Uploads kann im Formular die E-Mail-Benachrichtigung für ausgewählte Frontend-Mitgliedergruppen aktiviert werden. BEi Uploads werden Mitglieder per E-Mail benachrichtigt und erhalten in der E-Mail einen Link zum Event im Frontend.


### Event-Rating-Modul
Mit diesem Frontend-Modul können angemeldete Frontend-User einen Event anch dessen Ablauf bewerten (Star-Rating). Jeder User kann einen Event nur einmal bewerten. Der Durchschnitt wird dabei angezeigt.


### Event-Auflistungs-Template
Wir das im Modul genutzte Event-Auflistungs-Modul genutzt, werden die Events mit einem farbigen Punkt hervorgehoben.

- roter Punkt: Event noch nicht durchgeführt
- gelber Punkt: Event mit Bilduploads
- grüner Punkt: Event wurde bewertet.