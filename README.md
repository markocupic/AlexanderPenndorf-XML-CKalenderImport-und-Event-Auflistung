# ckalender_xml_event_import
Dieses Modul erm�glicht die Synchronisierung von Events auf ckalender.de nach tl_calendar_events im CMS Contao.

In den Kalendereinstellungen muss die Synchronisation aktiviert werden. Dabei muss eine url zur ckalender.de XML-Schnittstelle angegeben werden.
Die URL-Parameter �VonDatumD� und �BisDatumD� k�nnen mit dem Platzhalter ###+*days### oder ###-*days### dynamisch hinterlegt werden. Wobei * f�r die Anzahl Tage ab heute steht.
Mit dem Aktualisierungsintervall kann gesteurt werden, in welchen Abst�nden der Synchronisierungsmechanismus ausgel�st werden soll.
Wird eine Synchronisierung ausgel�st, werden zun�chst alle Events des Kalenders auf unsichtbar gestellt. Danach werden die Contao Events mit den ckalender Events �berschrieben und wieder auf sichtbar gestellt. Die Contao-events, die in der XML-Datei nicht enthalten sind, bleiben auf unsichtbar. Die ckalender event-id entspricht dabei der tl_calendar_events.uuid.

Der Update-Prozess kann zus�tzlich mit dem CKalenderXMLEventImportBeforeUpdateHook-beeinflusst werden. Die Methode erwartet 4 Parameter ($arrSet, $child, $xml, $xmlString) und gibt als R�ckgabewert $arrSet zur�ck.


# custom-events
Dieses Modul erweteitert das Contao Kalender Modul um weitere Funktionalit�ten.

### Bildupload in der Detailansicht des Events.
Angemeldete Benutzer k�nnen zum ausgew�hlten Event Bilder hochladen. Die Bilder werden nach dem Upload ins Verzeichnis files/events/event-id verschoben.
Das Verzeichnis kann in der config.php des Moduls angepasst werden.
Diese Bilder k�nnen dann mit dem daf�r angefertigten Modul in der Detailansicht angezeigt werden. Bilder k�nnen direkt aus dem Frontend auch wieder gel�scht werden.

Bei Uploads kann im Formular die E-Mail-Benachrichtigung f�r ausgew�hlte Frontend-Mitgliedergruppen aktiviert werden. BEi Uploads werden Mitglieder per E-Mail benachrichtigt und erhalten in der E-Mail einen Link zum Event im Frontend.


### Event-Rating-Modul
Mit diesem Frontend-Modul k�nnen angemeldete Frontend-User einen Event anch dessen Ablauf bewerten (Star-Rating). Jeder User kann einen Event nur einmal bewerten. Der Durchschnitt wird dabei angezeigt.


### Event-Auflistungs-Template
Wir das im Modul genutzte Event-Auflistungs-Modul genutzt, werden die Events mit einem farbigen Punkt hervorgehoben.

- roter Punkt: Event noch nicht durchgef�hrt
- gelber Punkt: Event mit Bilduploads
- gr�ner Punkt: Event wurde bewertet.