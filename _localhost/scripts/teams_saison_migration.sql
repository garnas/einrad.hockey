-- Migration: teams_saison
--
-- Ersetzt teams_name_historic durch eine Saison-Zugehörigkeitstabelle:
-- Eine Zeile bedeutet "dieses Team war in dieser Saison Ligateam und hieß so".
--
--   * Zeilen werden nie gelöscht, wenn ein Team die Liga verlässt.
--   * Die laufende Saison hat ebenfalls Zeilen (Saisonstart, Neuanmeldung, Umbenennung).
--
-- teams_name_historic bleibt vorerst unverändert bestehen, bis der Code umgestellt ist.
-- Das Skript ist wiederholbar (IF NOT EXISTS / INSERT IGNORE).

SET @aktuelle_saison = 32; -- = Config::SAISON

CREATE TABLE IF NOT EXISTS `teams_saison` (
  `saison` int(11) NOT NULL,
  `team_id` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  PRIMARY KEY (`saison`, `team_id`),
  KEY `team_id` (`team_id`),
  CONSTRAINT `teams_saison_ibfk_1` FOREIGN KEY (`team_id`) REFERENCES `teams_liga` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- 1) Bestehende historische Namen übernehmen
INSERT IGNORE INTO teams_saison (saison, team_id, name)
SELECT h.saison, h.team_id, h.name
FROM teams_name_historic h;

-- 2) Vergangene Saisons: Ligateams mit Ergebnis, aber ohne Eintrag ergänzen.
--    Name = letzter bekannter Name davor, sonst der aktuelle Teamname.
INSERT IGNORE INTO teams_saison (saison, team_id, name)
SELECT DISTINCT
    tl.saison,
    te.team_id,
    COALESCE(
        (SELECT h.name FROM teams_name_historic h
         WHERE h.team_id = te.team_id AND h.saison < tl.saison
         ORDER BY h.saison DESC LIMIT 1),
        t.teamname
    )
FROM turniere_ergebnisse te
INNER JOIN turniere_liga tl ON tl.turnier_id = te.turnier_id
INNER JOIN teams_liga t ON t.team_id = te.team_id
WHERE t.ligateam = 'Ja'
  AND tl.saison < @aktuelle_saison;

-- 3) Laufende Saison: alle aktiven Ligateams ...
INSERT IGNORE INTO teams_saison (saison, team_id, name)
SELECT @aktuelle_saison, t.team_id, t.teamname
FROM teams_liga t
WHERE t.aktiv = 'Ja' AND t.ligateam = 'Ja';

-- ... und Ligateams, die in der laufenden Saison schon gespielt haben, aber inzwischen inaktiv sind.
INSERT IGNORE INTO teams_saison (saison, team_id, name)
SELECT DISTINCT @aktuelle_saison, t.team_id, t.teamname
FROM turniere_ergebnisse te
INNER JOIN turniere_liga tl ON tl.turnier_id = te.turnier_id
INNER JOIN teams_liga t ON t.team_id = te.team_id
WHERE t.ligateam = 'Ja'
  AND tl.saison = @aktuelle_saison;
