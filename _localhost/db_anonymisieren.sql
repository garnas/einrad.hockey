-- Tabellen löschen
TRUNCATE TABLE turniere_log;
TRUNCATE TABLE spieler_zeitstrafen;
TRUNCATE TABLE teams_strafen;
TRUNCATE TABLE neuigkeiten;
TRUNCATE TABLE ligakarte_gesuch;
TRUNCATE TABLE mailbot;
TRUNCATE TABLE abstimmung_ergebnisse;
TRUNCATE TABLE abstimmung_teams;
# TRUNCATE TABLE oeffi_challenge;
TRUNCATE TABLE spieler_ausleihen;
TRUNCATE TABLE turniere_berichte;
TRUNCATE TABLE turniere_geloescht;
TRUNCATE `schiri_ergebnis`;
TRUNCATE `schiri_test`;
TRUNCATE TABLE abstimmung_teams;
TRUNCATE TABLE abstimmung_ergebnisse;
-- Spieler
UPDATE spieler SET vorname= 'Vorname',nachname='Nachname',jahrgang= 2020, geschlecht = 'd', letzte_saison = '27', timestamp = NULL;

-- Ligaleitung
UPDATE ligaleitung SET passwort = NULL, email = 'test@einrad.hockey';
INSERT INTO spieler (spieler_id, team_id, vorname, nachname, jahrgang, geschlecht, letzte_saison, timestamp) VALUES (3, NULL, 'Vorname', 'Entwickler', '1234', 'd', 30, current_timestamp());
INSERT INTO ligaleitung(funktion, login, passwort, spieler_id, email) VALUES ('ligaausschuss', 'entwickler', '$2y$10$ErHYjOnzowhPpBAqWRjOG.YsaaBJiR/IybP.1pdyWt4MBPlKNpN5e', '3', 'test@einrad.hockey');
INSERT INTO ligaleitung(funktion, login, passwort, spieler_id, email) VALUES ('oeffentlichkeitsausschuss', 'oeffentlichkeit', '$2y$10$ErHYjOnzowhPpBAqWRjOG.YsaaBJiR/IybP.1pdyWt4MBPlKNpN5e', '3', 'test@einrad.hockey');

-- Turniere
UPDATE turniere_details SET organisator='Vorname Nachname', handy='1234 56789', hinweis='Beispieltext';

-- Teams
UPDATE teams_liga SET passwort='$2y$10$PCOkOZQw6wut4uRCQyLWgup7nFn6Gv0zbqJ8ZhEds7wZ753Ba7wd6', freilose='1', zweites_freilos = NULL;
UPDATE teams_liga SET passwort_geaendert='Ja' WHERE aktiv = 'Ja';
UPDATE teams_details SET ligavertreter='Vorname Nachname', homepage='https://test.einrad.hockey', teamfoto=NULL;
UPDATE teams_kontakt SET email='test@einrad.hockey';

-- Neuigkeiten
INSERT INTO neuigkeiten (neuigkeiten_id, titel, inhalt, link_pdf, link_jpg, bild_verlinken, eingetragen_von, zeit) VALUES (NULL, 'Passwörter für die Logins', '<u>Teamcenter</u>\nName: <i>für jedes Team</i>\nPasswort: <b>test</b>\n\n<u>Ligacenter</u>\nName: <b>entwickler</b>\nPasswort: <b>einrad</b>\n\n<u>Öffentlichkeitscenter</u>\nName: <b>oeffentlichkeit</b>\nPasswort: <b>einrad</b>\n\nIm _localhost-Ordner befindet sich ein Tutorial, wie man eine Seite erstellt.', '', '', '', 'Ligaausschuss', current_timestamp());
