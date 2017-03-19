# ckalender_xml_event_import
Dieses Modul ermöglicht die Synchronisierung von Events auf ckalender.de nach tl_calendar_events im CMS Contao.

In den Kalendereinstellungen muss die Synchronisation aktiviert werden. Dabei muss eine url zur ckalender.de XML-Schnittstelle angegeben werden.
Die URL-Parameter „VonDatumD“ und „BisDatumD“ können mit dem Platzhalter ###+*days### oder ###-*days### dynamisch hinterlegt werden. Wobei * für die Anzahl Tage ab heute steht.
Mit dem Aktualisierungsintervall kann gesteurt werden, in welchen Abständen der Synchronisierungsmechanismus ausgelöst werden soll.
Wird eine Synchronisierung ausgelöst, werden zunächst alle Events des Kalenders auf unsichtbar gestellt. Danach werden die Contao Events mit den ckalender Events überschrieben und wieder auf sichtbar gestellt. Die Contao-events, die in der XML-Datei nicht enthalten sind, bleiben auf unsichtbar. Die ckalender event-id entspricht dabei der tl_calendar_events.uuid.

Der Update-Prozess kann zusätzlich mit dem CKalenderXMLEventImportBeforeUpdateHook-beeinflusst werden. Die Methode erwartet 4 Parameter ($arrSet, $child, $xml, $xmlString) und gibt als Rückgabewert $arrSet zurück.

![Kalendereinstellungen](manual/images/tl_calendar.png?raw=true "Kalendereinstellungen")
Kalendereinstellungen

# custom-events
Dieses Modul erweitert das Contao Kalender Modul um weitere Funktionalitäten. Es beinhaltet im Wesentlichen den Event-Fileupload, 3 Frontend Module und 2 Event-Templates (Event-Auflistung und Eventreader).

### Bildupload in der Detailansicht des Events.
Angemeldete Benutzer können zum ausgewählten Event Bilder hochladen. Die Bilder werden nach dem Upload ins Verzeichnis files/events/event-id verschoben.
Das Verzeichnis kann in der config.php des Moduls angepasst werden.
Diese Bilder können dann mit dem dafür angefertigten Modul in der Detailansicht angezeigt werden. Bilder können direkt aus dem Frontend auch wieder gelöscht werden. Für den Upload muss im Formulargenerator ein Formular erstellt werden. Als Fileuploader wählt man den Fineuploader.

![Upload-Formular](manual/images/upload_form.png?raw=true "Upload-Formular")
Bei Uploads kann im Formular die E-Mail-Benachrichtigung für ausgewählte Frontend-Mitgliedergruppen aktiviert werden. Bei Uploads werden Mitglieder per E-Mail benachrichtigt und erhalten in der E-Mail einen Link zum Event im Frontend.

![Bildupload Formularfeld](manual/images/fileupload_field.png?raw=true "Bildupload Formularfeld")
Wichtig!!! Die Einstellungen für das Bildupload Formularfeld. Der Feldname muss zwingend **"event_image_uploader"** heissen! Als Uploadverzeichnis kann irgend ein Ordner gewählt werden,. Der Ordner dient lediglich als temporärer Zwischenspeicher. Die Bilder werden nach dem Upload nach files/events/event-id verschoben.




## Frontend Module

### Event-Galerie
Mit diesem Frontend-Modul lassen sich die zum Event hochgeladenen Bilder anzeigen.

### Event-Rating-Formular
Mit diesem Frontend-Modul können angemeldete Frontend-User einen Event nach dessen Ablauf bewerten (Star-Rating). Jeder User kann einen Event nur einmal bewerten. Der Durchschnitt wird dabei angezeigt.

### Event-Rating
Mit diesem Frontend-Modul lassen sich die Event-Ratings anzeigen (Durchschnittswert).

## Eventlisting und Eventreader Templates

### Eventlisting-Template (event_upcoming_custom)
![Event Listing Template](manual/images/event_listing_template.png?raw=true "Event Listing Template")
Wird das mitgelieferte Event-Auflistungs-Tempalte genutzt, werden die Events mit einem farbigen Punkt hervorgehoben.

- roter Punkt: Event noch nicht durchgeführt
- gelber Punkt: Event mit Bilduploads
- grüner Punkt: Event wurde bewertet.


### Eventreader-Template (event_full_custom)
![Event Reader Template](manual/images/event_reader_template.png?raw=true "Event Reader Template")
Das mitgelieferte Event-Reader Template ermöglicht die Ausgabe von weiteren Feldern, welche aus Ckalender.de importiert wurden.

