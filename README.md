# ckalender_xml_event_import
Dieses Modul erm�glicht die Synchronisierung von Events auf ckalender.de nach tl_calendar_events im CMS Contao.

In den Kalendereinstellungen muss die Synchronisation aktiviert werden. Dabei muss eine url zur ckalender.de XML-Schnittstelle angegeben werden.
Die URL-Parameter �VonDatumD� und �BisDatumD� k�nnen mit dem Platzhalter ###+*days### oder ###-*days### dynamisch hinterlegt werden. Wobei * f�r die Anzahl Tage ab heute steht.
Mit dem Aktualisierungsintervall kann gesteurt werden, in welchen Abst�nden der Synchronisierungsmechanismus ausgel�st werden soll.
Wird eine Synchronisierung ausgel�st, werden zun�chst alle Events des Kalenders auf unsichtbar gestellt. Danach werden die Contao Events mit den ckalender Events �berschrieben und wieder auf sichtbar gestellt. Die Contao-events, die in der XML-Datei nicht enthalten sind, bleiben auf unsichtbar. Die ckalender event-id entspricht dabei der tl_calendar_events.uuid.

Der Update-Prozess kann zus�tzlich mit dem CKalenderXMLEventImportBeforeUpdateHook-beeinflusst werden. Die Methode erwartet 4 Parameter ($arrSet, $child, $xml, $xmlString) und gibt als R�ckgabewert $arrSet zur�ck.

![Kalendereinstellungen](manual/images/tl_calendar.png?raw=true "Kalendereinstellungen")
Kalendereinstellungen

# custom-events
Dieses Modul erweitert das Contao Kalender Modul um weitere Funktionalit�ten. Es beinhaltet im Wesentlichen den Event-Fileupload, 3 Frontend Module und 2 Event-Templates (Event-Auflistung und Eventreader).

### Bildupload in der Detailansicht des Events.
Angemeldete Benutzer k�nnen zum ausgew�hlten Event Bilder hochladen. Die Bilder werden nach dem Upload ins Verzeichnis files/events/event-id verschoben.
Das Verzeichnis kann in der config.php des Moduls angepasst werden.
Diese Bilder k�nnen dann mit dem daf�r angefertigten Modul in der Detailansicht angezeigt werden. Bilder k�nnen direkt aus dem Frontend auch wieder gel�scht werden. F�r den Upload muss im Formulargenerator ein Formular erstellt werden. Als Fileuploader w�hlt man den Fineuploader.

![Upload-Formular](manual/images/upload_form.png?raw=true "Upload-Formular")
Bei Uploads kann im Formular die E-Mail-Benachrichtigung f�r ausgew�hlte Frontend-Mitgliedergruppen aktiviert werden. Bei Uploads werden Mitglieder per E-Mail benachrichtigt und erhalten in der E-Mail einen Link zum Event im Frontend.

![Bildupload Formularfeld](manual/images/fileupload_field.png?raw=true "Bildupload Formularfeld")
Wichtig!!! Die Einstellungen f�r das Bildupload Formularfeld. Der Feldname muss zwingend **"event_image_uploader"** heissen! Als Uploadverzeichnis kann irgend ein Ordner gew�hlt werden,. Der Ordner dient lediglich als tempor�rer Zwischenspeicher. Die Bilder werden nach dem Upload nach files/events/event-id verschoben.




## Frontend Module

### Event-Galerie
Mit diesem Frontend-Modul lassen sich die zum Event hochgeladenen Bilder anzeigen.

### Event-Rating-Formular
Mit diesem Frontend-Modul k�nnen angemeldete Frontend-User einen Event nach dessen Ablauf bewerten (Star-Rating). Jeder User kann einen Event nur einmal bewerten. Der Durchschnitt wird dabei angezeigt.

### Event-Rating
Mit diesem Frontend-Modul lassen sich die Event-Ratings anzeigen (Durchschnittswert).

## Eventlisting und Eventreader Templates

### Eventlisting-Template (event_upcoming_custom)
![Event Listing Template](manual/images/event_listing_template.png?raw=true "Event Listing Template")
Wird das mitgelieferte Event-Auflistungs-Tempalte genutzt, werden die Events mit einem farbigen Punkt hervorgehoben.

- roter Punkt: Event noch nicht durchgef�hrt
- gelber Punkt: Event mit Bilduploads
- gr�ner Punkt: Event wurde bewertet.


### Eventreader-Template (event_full_custom)
![Event Reader Template](manual/images/event_reader_template.png?raw=true "Event Reader Template")
Das mitgelieferte Event-Reader Template erm�glicht die Ausgabe von weiteren Feldern, welche aus Ckalender.de importiert wurden.

