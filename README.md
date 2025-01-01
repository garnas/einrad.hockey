# einrad.hockey-Website
![Logo der Einradhockeyliga](https://einrad.hockey/bilder/logo_lang_small.png)


php -d xdebug.mode=debug -d xdebug.client_host=127.0.0.1 -d xdebug.client_port=9003 -d xdebug.start_with_request=yes bin/doctrine

Dies ist der Code der Website [einrad.hockey](https://einrad.hockey) der Deutschen Einradhockeyliga.

einrad.hockey verwendet sein eigenes Framework. Dieses wurde von mir entwickelt, um Interessierten im Einradhockey 
nach einer möglichst kurzen Einrichtungs- und Einarbeitungsphase die Möglichkeit zu geben, an der Website mitzuarbeiten. 
Dafür sind nur grundlegende PHP- und/oder HTML/CSS-Kenntnisse notwendig, welche man sich schnell aneignen kann.

Du hast Lust mitzuwirken? Oder Fragen darüber, wie die Webseite funktioniert? Melde dich gerne bei ansgar@einrad.hockey

## Was kann einrad.hockey?

* Einstellen von Turnieren 
* Anmeldung von Teams zu Turnieren
* Automatische Erstellung von Spielplänen und Turnierreports
* Spiel- und Turnierergebnissen live eintragen
* Turnierergebnisse und Ligatabellen
* Teamdatenverwaltung
* Teamkaderverwaltung
* Kontaktcenter für die Teams untereinander
* Einstellen von Neuigkeiten mit sicheren Dateianhängen
* Google Maps Karte mit allen Ligateams
* MailBot zum Versenden von automatischen E-Mails
* LigaBot für die Phasenübergänge der Turniere und zum Losen von Spielen-Listen
* und vieles mehr!

## xml-Schnittstelle

* [https://www.einrad.hockey/xml/turnierliste.php](https://www.einrad.hockey/xml/turnierliste.php)
* [https://www.einrad.hockey/xml/turnieranmeldungen.php](https://www.einrad.hockey/xml/turnieranmeldungen.php)
* [https://www.einrad.hockey/xml/rangtabelle.php](https://www.einrad.hockey/xml/rangtabelle.php)
* [https://www.einrad.hockey/xml/spielplan.php?turnier_id=?](https://www.einrad.hockey/xml/spielplan.php?turnier_id=1021)

## dev-Umgebung erstellen
* Eine Beispiel php.ini mit den notwendigen Extensions und Debug-Settings liegt in _Localhost/php.ini-example

### Möglichkeit 1: Docker
> Siehe [docker-setup.md](docker-setup.md)

### Möglichkeit 2: XAMPP
1. Voraussetzungen:
   * XAMPP installieren (PHP >=8.2, Stand 30.12.2024 noch nicht für PHP 8.3 verfügbar), Composer installieren
   * Repository in den htdocs-Ordner herunterladen. Ordnerstruktur sollte so aussehen: 
    ```
    htdocs
    └── einrad.hockey
        └── example_env.php
    ```
   * Im Verzeichnis der example_env.php eine Datei env.php erstellen und den Inhalt von example_env.php hereinkopieren

2. Datenbank einrichten:
   * VIA XAMPP Control Panel, MySQL -> Admin -> phpMyAdmin eine Datenbank db_localhost erstellen
   * Die db_localhost.sql im Ordner _localhost in die Datenbank laden
   * In der oben erstellten env.php die Zugangsdaten der SQL-Datenbank eintragen (falls von den default Zugangsdaten abgewichen wird)

3. Abhängigkeiten installieren
   * Im Verzeichnis der composer.json via CLI "composer update" ausführen
   * Anschließend via CLI "composer dump-autoload" ausführen, um den Autoloader der Klassen zu konfigurieren

4. Seite öffnen
   * http://localhost/einrad.hockey/public/liga/neues.php
   * Hier sollte nun die Neuigkeitenseite angezeigt werden

### Ionos Webspace:
* composer.phar herunterladen
<pre>
curl -sS https://getcomposer.org/installer | /usr/bin/php8.3-cli
</pre>

* composer.phar ausführen
<pre>
/usr/bin/php8.3-cli composer.phar about
</pre>