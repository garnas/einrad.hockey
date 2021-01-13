# einrad.hockey-Webseite
![Logo der Einradhockeyliga](https://einrad.hockey/bilder/logo_lang_small.png)

Dies ist der Code der Webseite [einrad.hockey](https://einrad.hockey) der Deutschen Einradhockeyliga.

Du hast Lust mitzuwirken? Melde dich bei ansgar@einrad.hockey!

## Was kann einrad.hockey?

* Einstellen von Turnieren 
* Anmeldung von Teams zu Turnieren
* Automatische Erstellung von Spielplänen und Turnierreports
* Turnierergebnisse und Ligatabellen
* Teamdatenverwaltung
* Teamkaderverwaltung
* Kontaktcenter für die Teams untereinander
* Einstellen von Neuigkeiten mit sicheren Dateianhängen
* Google Maps Karte mit allen Ligateams
* MailBot zum versenden von automatischen E-Mails
* LigaBot für die Phasenübergänge der Turniere und zum Losen von Spielen-Listen
* und vieles mehr!

## Dokumente

[Allgemeine Dokumentation](http://einrad.hockey/dokumente/dokumetation/allgemeine_dokumentation.pdf)

[Entwicklungsumgebung erstellen](http://einrad.hockey/dokumente/dokumetation/entwicklungsumgebung_erstellen.pdf)

[Baustellen und Projekte](http://einrad.hockey/dokumente/dokumetation/baustellen_und_projekte.pdf)

## xml-Schnittstelle:

* [https://www.einrad.hockey/xml/turnierliste.php](https://www.einrad.hockey/xml/turnierliste.php)
* [https://www.einrad.hockey/xml/turnierliste.php](https://www.einrad.hockey/xml/turnieranmeldungen.php)
* [https://www.einrad.hockey/xml/turnierliste.php](https://www.einrad.hockey/xml/rangtabelle.php)



## Wunschliste
### Rundschreiben Archiv erstellen
Sammlung der Rundschreiben an die Liga auf einer Seite
### Erstellung eines neuen Archives
Für das neue Archiv sollte eine eigene Archivdatenbank erstellt werden.
### Statistikseite für die Teams
Die Tore der Teams werden jetzt in der Datenbank erfasst. Hierfür könnte man eine Statistikseite erstellen, welche im Teamcenter aufrufbar ist. (Gesamttorverhältnis, Spiel mit den meisten Toren, Team gegen welches man die meisten Tore pro Spiel gefangen hat, Prozent gewonnener Spiele, etc)
### Öffentliche Teamseite
Eine Seite wo Teams etwas über sich schreiben können und Bilder hochladen können.
### Banner für „Live-Turnierergebnisse“ auf der Startseite
Wenn ein Ausrichter gerade Live-Ergebnisse eines Turnieres einträgt, sollte 
### Unterstützung der Schiedsrichterausbildung
Verwaltung von Schiedsrichterausbildung (Ausbilder via Kontaktformular anschreiben, eintragen von Ausbildungen, besseres Bewerben der Schiedsrichterausbildung auf der Webseite, Schiedsrichtertest digitalisieren?)
### Verbesserung der Useability von einrad.hockey
Die ersten Erfahrungen mit der Webseite nutzen, um diese weiter zu verbessern. (Menüführung, Einradhockey
### Neues Logo? Weihnachtsskin/Saisonpausenskin für die Webseite?
### Design-Overhaul?
### Seite mit Einradhockeytipps
### Direkter Kalendereintrag eines Turniers
### Telegram/WhatsApp Schnittstelle?

## Schleifenliste

* Spielpläne für 8er Turniere

* Turnier mit 8-Plätzen aber weniger angemeldeten Teams -> Fehler bei der dynamischen jgj-Spielplanerstellung

* Sonderfälle für Turniere korrekt auf der Ergebnisseite des Turniers anzeigen lassen. Konkretes Beispiel: Teams reisen ab, bevor es zum Penaltyschießen kam. Zurzeit ist es möglich entweder Spielpläne/Ergebnisse in die Datenbank einzutragen, oder manuell ein PDF oder XLSX als Spielplan/Ergebnisdatei hochzuladen.

* (Vorschlag) Spielpläne nur erstellen, wenn er im Ligacenter oder per Cronjob erstellt wird. Zurzeit wird er beim ersten Aufrufen des dynamischen Links erstellt – dies kann zur Erstellung ungewünschter Spielpläne in der Datenbank führen.

* Spielzeiten und Puffer könnte für jedes Turnier einzeln erstellt werden (Philipp).

* Weiterführende Shortcut-Links für dynamische Turnierseiten sollten einheitlich und automatisch erstellt werden und am besten als Funktion der Form-Klasse in die Templates eingefügt werden. Zurzeit sind diese jeweils einzeln im Layoutteil der public-Dateien eingetippt.

* Eine Methode zum „Übersetzen“ der Datenbankbegriffe von Turnieren in Sprache (z. B. „melde“ in Meldephase.) Zurzeit wird dies „manuell“ im Logikteil gemacht.
Nicht PHP-Lastig

* Html-Datalistelemente für den Teamlogin funktionieren nicht auf den neuen Firefox-Mobil-Browser. Dies gilt allerdings für alle Datalistelemente im Internet in manchen mobilen Firefox-Browser

* Verbesserung des SEO-Ratings von einrad.hockey (Aktuell Platz 2 (6.11.20) für einradhockey)
