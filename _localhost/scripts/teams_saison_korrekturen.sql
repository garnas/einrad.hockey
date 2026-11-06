-- Korrekturen an teams_saison nach teams_saison_migration.sql
-- Wiederholbar (INSERT IGNORE).

-- Saison 25 (2020, Corona): Es gab nur kurz Turniere. Teams, die nicht gespielt haben,
-- wurden automatisch in die Saison 26 (2020/21) übernommen. Sie gehören trotzdem zur Saison 25.
-- Diese Teams hatten einen Eintrag in Saison 24 und 26, aber keinen in Saison 25.
INSERT IGNORE INTO teams_saison (saison, team_id, name)
SELECT 25, ts.team_id, ts.name
FROM teams_saison ts
WHERE ts.saison = 24
  AND ts.team_id IN (18, 40, 161, 306, 320, 335, 427, 611, 624, 696, 717, 849);

-- Saison 23 (2018): SKV Mörfelden Joker (16) war in der Liga, Eintrag fehlte.
INSERT IGNORE INTO teams_saison (saison, team_id, name)
SELECT 23, ts.team_id, ts.name
FROM teams_saison ts
WHERE ts.saison = 22
  AND ts.team_id = 16;

-- TV Lilienthal Nordlichter (833): nur in Saisons mit Ergebnissen Ligateam (21/22, 22/23).
DELETE ts FROM teams_saison ts
WHERE ts.team_id = 833
  AND NOT EXISTS (
      SELECT 1 FROM turniere_ergebnisse te
      INNER JOIN turniere_liga tl ON tl.turnier_id = te.turnier_id
      WHERE te.team_id = ts.team_id AND tl.saison = ts.saison
  );

-- Querrad: Die Ergebnisse der Saison 24 (2019) lagen auf einem Hilfsteam 849 "Querrad (Erg. n. übern.)",
-- damit sie nicht in die Folgesaisons übernommen werden. Dafür gibt es inzwischen das Flag
-- saison_uebernahme_verhindern. Ergebnisse zurück auf Querrad (20) mit Flag, Hilfsteam löschen.
-- teams_saison- und teams_name_historic-Einträge von 849 werden per ON DELETE CASCADE entfernt.
UPDATE turniere_ergebnisse
SET team_id = 20, saison_uebernahme_verhindern = 1
WHERE team_id = 849;

DELETE FROM teams_liga WHERE team_id = 849;

-- Uni(cycle) School Dresden (984): Hieß die ganze Saison 31 (25/26) noch so (siehe Turnierlogs).
-- Der Snapshot der Saison 31 wurde erst nach der Umbenennung in "Dresdner Einradpanther" erstellt.
UPDATE teams_saison
SET name = 'Uni(cycle) School Dresden'
WHERE team_id = 984 AND saison = 31;
