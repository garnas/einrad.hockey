# einrad.hockey

![Logo der Einradhockeyliga](https://einrad.hockey/bilder/logo_lang_small.png)

Dies ist der Code der Website [einrad.hockey](https://einrad.hockey) der Deutschen Einradhockeyliga.

Du hast Lust mitzuwirken? Oder Fragen darüber, wie die Webseite funktioniert? Melde dich gerne bei uns.

---

## Features

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

---

# Entwicklungsumgebung aufsetzen mit Dev-Container

## Voraussetzungen

- Docker installiert und den Docker Daemon gestartet (z. B. via Docker Desktop)
- Repository lokal klonen

## Enthaltene Dienste

- PHP 8.3, Apache, MariaDB und phpMyAdmin
- Datenbank wird beim ersten Start automatisch aus `_localhost/db_localhost.sql` befüllt
- Debugging mit Breakpoints direkt einsatzbereit

| Dienst     | URL                   | Info                              |
|------------|-----------------------|-----------------------------------|
| Webseite   | http://localhost      | einrad.hockey Website (Port 80)   |
| phpMyAdmin | http://localhost:8081 | Datenbank GUI Tool (Port 8081)    |
| MariaDB    | localhost:3306        | DB-Verbindung (in Docker db:3306) |


## Mit Visual Studio Code

**Voraussetzungen:** [VS Code](https://code.visualstudio.com/) + Extension [Dev Containers](https://marketplace.visualstudio.com/items?itemName=ms-vscode-remote.remote-containers)

1. Projekt in VS Code öffnen.
2. Popup "Reopen in Container" bestätigen — oder: `F1` → `Dev Containers: Open Folder in Container`.
3. VS Code baut den Container und führt automatisch `setup.sh` aus (Composer-Install, Doctrine-Setup).

Nach dem Start öffnet VS Code automatisch http://localhost im Browser.

**Vorinstallierte Extensions:**
- **Intelephense** — PHP-Autocompletion und Analyse
- **PHP Debug** — Xdebug-Integration

## Mit PHPStorm

**Voraussetzungen:** PHPStorm 2023.2 oder neuer

1. Projekt in PHPStorm öffnen.
2. PHPStorm erkennt `.devcontainer/devcontainer.json` automatisch → Popup "Dev Containers" → Container starten.
   - Alternativ: `Strg+Shift+A` → `Dev Containers` oder via Services.

---

# Wichtige Commands

### Tests

```shell
vendor/bin/phpunit tests/
```

> Für die Integrationstests muss die Datenbank in `env.php` konfiguriert sein.

### Dependencies

```shell
composer install
composer dump-autoload
```

> `dump-autoload` nach neu erstellten oder umbenannten Klassen ausführen.

### Doctrine Cache aktualisieren

```shell
php bin/doctrine orm:clear-cache:metadata
php bin/doctrine orm:clear-cache:query
php bin/doctrine orm:generate-proxies
```

> Nötig bei Änderungen an Entities, damit das Mapping korrekt aktualisiert wird.

### Doctrine CLI mit Xdebug

```shell
php -d xdebug.mode=debug -d xdebug.client_host=127.0.0.1 -d xdebug.client_port=9003 -d xdebug.start_with_request=yes bin/doctrine
```

### Docker zurücksetzen (alles löschen)

```bash
docker rm -vf $(docker ps -aq)
docker rmi -f $(docker images -aq)
docker system prune --all --volumes
```

---

# Ionos Webspace

### Composer einrichten

```shell
# composer.phar herunterladen
curl -sS https://getcomposer.org/installer | /usr/bin/php8.3-cli

# Ausführen
/usr/bin/php8.3-cli composer.phar about
```

### Doctrine Cache aktualisieren

```shell
/usr/bin/php8.3-cli bin/doctrine orm:clear-cache:metadata
/usr/bin/php8.3-cli bin/doctrine orm:clear-cache:query
/usr/bin/php8.3-cli bin/doctrine orm:generate-proxies
```

# XML-Schnittstelle

| Endpunkt                                                                                            | Beschreibung             |
|-----------------------------------------------------------------------------------------------------|--------------------------|
| [`/xml/turnierliste.php`](https://www.einrad.hockey/xml/turnierliste.php)                           | Alle Turniere            |
| [`/xml/turnieranmeldungen.php`](https://www.einrad.hockey/xml/turnieranmeldungen.php)               | Turnieranmeldungen       |
| [`/xml/rangtabelle.php`](https://www.einrad.hockey/xml/rangtabelle.php)                             | Rangtabelle              |
| [`/xml/spielplan.php?turnier_id=1021`](https://www.einrad.hockey/xml/spielplan.php?turnier_id=1021) | Spielplan eines Turniers |