# Running in VSCode Dev Containers

## Voraussetzungen
- Docker Desktop (installiert und gestartet)
- Visual Studio Code
- VS Code Extension: Dev Containers

## Workflow
1. Erstelle einen lokalen Clone des Repository.
2. Öffne VS Code.
3. Nach dem Öffnen des Repository-Ordners erscheint ein Popup: "Reopen in Container".
   - Alternativ: F1 → "Dev Containers: Open Folder in Container".
4. VS Code baut und startet den Container. Dein Repository wird automatisch als Workspace gemountet.

### Code aktualisieren (im Container)
```bash
git pull
composer install  # Falls sich die composer.json geändert hat
```

### Zugriff auf die Dienste

Dienst     | URL                   | Info
-----------|-----------------------|----------------------------
Webseite   | http://localhost      | Hauptanwendung auf Port 80
phpmyadmin | http://localhost:8081 | Datenbank-Verwaltung


### DB-Zugangsdaten
+ Host: db
+ User: root
+ Passwort: root
+ Datenbank: db_localhost

⚠️ Hinweis: Diese Zugangsdaten gelten nur für die Entwicklungsumgebung im Devcontainer.
