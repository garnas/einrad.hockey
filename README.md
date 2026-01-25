# Fedora

# PHP installieren

> https://www.php.net/downloads?usage=web&os=linux&osvariant=linux-fedora&version=8.3

```shell
# Add the Remi's RPM repository.
sudo dnf install -y dnf-plugins-core
sudo dnf install -y https://rpms.remirepo.net/fedora/remi-release-$(rpm -E %fedora).rpm
sudo dnf module reset php -y
sudo dnf module enable php:remi-8.3 -y

# Install PHP (single/default version).
sudo dnf install -y php

# Install Extensions
sudo dnf -y install php-mysql
sudo dnf -y install php-tokenizer
sudo dnf -y install php-sysvsem
sudo dnf -y install php-sockets
sudo dnf -y install php-readline
sudo dnf -y install php-posix
sudo dnf -y install php-phar
sudo dnf -y install php-mbstring
sudo dnf -y install php-intl
sudo dnf -y install php-iconv
sudo dnf -y install php-ftp
sudo dnf -y install php-fileinfo
sudo dnf -y install php-ffi
sudo dnf -y install php-exif
sudo dnf -y install php-curl
sudo dnf -y install php-ctype
sudo dnf -y install php-calendar
sudo dnf -y install php-pdo
sudo dnf -y install php-mysql
sudo dnf -y install php-gd
sudo dnf -y install php-exif
sudo dnf -y install php-mbstring
sudo dnf -y install php-xsl
sudo dnf -y install php-zip
sudo dnf -y install php-xdebug
```

# Composer installieren
> https://getcomposer.org/download/
```shell
   php -r "copy('https://getcomposer.org/installer', 'composer-setup.php');"
   php -r "if (hash_file('sha384', 'composer-setup.php') === 'c8b085408188070d5f52bcfe4ecfbee5f727afa458b2573b8eaaf77b3419b0bf2768dc67c86944da1544f06fa544fd47') { echo 'Installer verified'.PHP_EOL; } else { echo 'Installer corrupt'.PHP_EOL; unlink('composer-setup.php'); exit(1); }"
   php composer-setup.php
   php -r "unlink('composer-setup.php');"
```

# Composer dependencys installieren
```shell
   sudo php composer.phar install
   sudo php composer.phar dump-autoload
```

# Docker

> https://docs.docker.com/engine/install/fedora/#installation-methods

```shell
    sudo dnf config-manager addrepo --from-repofile https://download.docker.com/linux/fedora/docker-ce.repo
    sudo dnf -y install docker-ce docker-ce-cli containerd.io docker-buildx-plugin docker-compose-plugin
    sudo systemctl enable --now docker
```

# Database
```shell
    # Datenbank starten
    sudo docker compose up mariadb -d
    (echo "CREATE DATABASE IF NOT EXISTS db_localhost; USE db_localhost;"; cat ./_localhost/db_localhost.sql) | sudo docker exec -i ligaseite-mariadb mariadb -u root -proot
```

# Enviroment
```shell
    # env.php erstellen
    cp example_env.php env.php
```

# Class loader
```shell
    # env.php erstellen
    cp example_env.php env.php
```

# einrad.hockey-Website
![Logo der Einradhockeyliga](https://einrad.hockey/bilder/logo_lang_small.png)

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

```shell
composer install
composer dump-autoload
```

> Composer dump-autoload ist ebenfalls wichtig, damit neu erstellte oder umbenannte Klassen in unserem Code richtig geladen werden ;)

4. Doctrine Cache Update
```shell
sudo php bin/doctrine orm:clear-cache:metadata
sudo php bin/doctrine orm:clear-cache:query
sudo php bin/doctrine orm:generate-proxies
```

5. Seite öffnen
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

* Doctrine Cache Update
```shell
/usr/bin/php8.3-cli bin/doctrine orm:clear-cache:metadata
/usr/bin/php8.3-cli bin/doctrine orm:clear-cache:query
/usr/bin/php8.3-cli bin/doctrine orm:generate-proxies
```

## Run tests
* Für die Integrationstests muss die Datenbank in der env.php aufgesetzt sein.

```shell
   vendor/bin/phpunit tests/
```

## Sonstiges
* Eine vorkonfigurierte php.ini ist in _localhost zu finden.

* Doctrine CLI Debug
   ```
   php -d xdebug.mode=debug -d xdebug.client_host=127.0.0.1 -d xdebug.client_port=9003 -d xdebug.start_with_request=yes bin/doctrine
   ```