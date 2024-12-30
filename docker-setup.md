# Voraussetzungen
Eingerichtet wurde Docker auf Windows mit Hilfe von
- Docker Desktop
- Windows Subsystem for Linux (WSL2)
Letzteres wird automatisch installiert / benötigt, wenn Docker Desktop installiert wird, da Docker nur auf Linux funktioniert.

# Files
## Dockerfile
Der Dockerfile stellt sicher, dass alle Composer-Dependencies installiert sind, die für die Ligaseite verwendet werden. Hinzu kommt eine Config-Datei, die den Zugriff auf die Webseite ermöglicht. *Änderungen an den Dependencies erfordern ein neues Bauen des Images!*

## Docker Compose
Stellt neben dem Webserver die Datenbank und phpmyadmin als Client zur Verfügung. Hier wird die Codebasis an das Image gemountet, sodass Änderungen abgebildet werden können. Zudem ist sichergestellt, dass die Umgebung der Produktion entspricht.

# Setup
## Änderungen an der env.php
- `BASE_URL = 'http://localhost'`
- `HOST_NAME = 'ligaseite-mariadb'`
- `DATABASE = 'db_localhost'`
- `USER_NAME = 'root'`
- `PASSWORD = 'root'`

## Docker
- Starten: `docker compose up -d`
  - Die Seite ist erreichbar: http://localhost:80
  - phpmyadmin ist erreichbar: http://localhost:8080
- Beenden: `docker compose down`: Die Daten in der Datenbank bleiben erhalten
- Beenden: `docker compose down -v`: Die Datenbank wird vollständig zurückgesetzt
