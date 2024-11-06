# Voraussetzungen
Eingerichtet wurde Docker auf Windows mit Hilfe von
- Docker Desktop
- Windows Subsystem for Linux (WSL2)
Letzteres wird automatisch installiert / benötigt, wenn Docker Desktop installiert wird, da Docker nur auf Linux funktioniert.

# Files
Im Repo befinden sich nun neue Files, die Docker und alle weiteren Komponenten einrichten

## Dockerfile
Auf Basis eines PHP-Apache Images wird ein Apache-Server erstellt, welcher als Webserver dient. Dabei werden jedoch Installationen von Composer und weiteren Dependencies notwendig. Diese werden vom Dockerfile vorgenommen. Dafür werden die notwendigen composer-Files in das Image kopiert. Zudem wird der gesamte Code in an das Image gemounted, sodass Codeänderungen *ohne Neustart* des Containers auf der Seite abgebildet werden können

## Docker Compose
Es wird ein Netzwerk aus unterschiedlichen Containern erstellt. Diese sind der Webserver (siehe Dockerfile), ein MySQL-Server und phpmyadmin, für einen grafischen Zugriff auf die Datenbank. Darüber hinaus wird der "Dummy-Datensatz" der Datenbank automatisch auf den MySQL-Server geschrieben.

## Dockerignore
Ist vergleichbar mit einem gitignore und beschreibt die Files, die von Docker ignoriert weden können oder müssen. Dies ist vor allem für die Composer-Dependencies notwendig.

# Setup
## Code-Änderungen
- In der `env.php` muss die `BASE_URL` auf `'http://localhost'` gesetzt werden.
- Zudem muss (zur Zeit) sichergestellt werden, dass in der `env.php` `DATABASE` auf `db_localhost` gesetzt ist!
- Es war notwendig Änderungen am Dummy vorzunehmen, da dieser sonst nicht richtig übernommen werden kann.

## Docker
Über die Kommandozeile kann im Repository docker verwendet werden. Wie genau das mit Docker Desktop geht - keine Ahnung. Initial muss das Image erstellt werden. Dies geht über
`docker build .`
Im Anschluss kann bereits das Netzwerk erstellt erden. Dies geht via
`docker compose up -d`
Dabei wird in Docker ein Volume erzeugt, welches die Daten der Datenbank speichert. Wird der Container beendet, gehen Änderungen an der DB *nicht* verloren! Das Netzwerk kann gestoppt werden mit
`docker compose down`
Soll das Volume gelöscht und die Datenbank beim nächsten Start neu erstellt werden, so muss folgender Befehl genutzt werden:
`docker compose down -v`

## Aufrufen im Browser
Die Webseite ist nun lokal unter `http://localhost/liga/neues.php` erreichbar.

# Hinweis :warning:
Die Umsetzungen von Docker sind absolut rudimentär und zum Teil Mittel zum Zweck. Hier muss sicherlich nochmal aufgeräumt und verbessert werden. Z.B. BASE_URL und Environment-Variablen.
