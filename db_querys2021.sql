ALTER TABLE `turniere_liga` CHANGE `spieltag` `spieltag` INT(11) NOT NULL DEFAULT '0';
ALTER TABLE `turniere_details` CHANGE `link_spielplan` `link_spielplan` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `turniere_details` CHANGE `link_spielplan` `link_spielplan` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `teams_liga` CHANGE `freilose` `freilose` INT(11) NULL;
ALTER TABLE `teams_liga` CHANGE `passwort` `passwort` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `mailbot` CHANGE `fehler` `fehler` VARCHAR(300) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT '';
ALTER TABLE `teams_details` CHANGE `plz` `plz` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `ort` `ort` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `verein` `verein` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `homepage` `homepage` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `ligavertreter` `ligavertreter` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL, CHANGE `teamfoto` `teamfoto` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE `turniere_ergebnisse` CHANGE `ergebnis` `ergebnis` INT(11) NULL;
ALTER TABLE `teams_details` ADD `trikot_farbe_1` VARCHAR(9) NULL AFTER `teamfoto`, ADD `trikot_farbe_2` VARCHAR(9) NULL AFTER `trikot_farbe_1`;
ALTER TABLE `turniere_liste` CHANGE `position_warteliste` `position_warteliste` INT(11) NULL;
ALTER TABLE `plz` CHANGE `PLZ` `PLZ` VARCHAR(5) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE `Ort` `Ort` TEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;
ALTER TABLE `spieler` CHANGE `vorname` `vorname` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE `nachname` `nachname` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE `geschlecht` `geschlecht` ENUM('m','w','d') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL, CHANGE `schiri` `schiri` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '', CHANGE `junior` `junior` ENUM('Ja','Nein','') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT 'Nein';
ALTER TABLE `neuigkeiten` CHANGE `inhalt` `inhalt` VARCHAR(1800) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE `spieler` CHANGE `zeit` `zeit` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
UPDATE `spieler` SET `zeit`= NULL WHERE zeit = '0000-00-00 00:00:00';

ALTER TABLE `spieler` CHANGE `team_id` `team_id` INT(11) NULL;
ALTER TABLE `spieler` CHANGE `letzte_saison` `letzte_saison` INT(11) NULL;

CREATE TABLE `ligaleitung` (
 `ligaleitung_id` int(11) NOT NULL AUTO_INCREMENT,
 `spieler_id` int(11) NOT NULL,
 `funktion` enum('ligaausschuss','schiriausschuss','oeffentlichkeitsausschuss','technikausschuss','schiriausbilder') NOT NULL,
 `email` varchar(255) DEFAULT NULL,
 `login` varchar(255) DEFAULT NULL,
 `passwort` varchar(255) DEFAULT NULL,
 PRIMARY KEY (`ligaleitung_id`),
 UNIQUE KEY `login` (`login`),
 KEY `spieler_id` (`spieler_id`),
 CONSTRAINT `ligaleitung_ibfk_1` FOREIGN KEY (`spieler_id`) REFERENCES `spieler` (`spieler_id`)
);

DELETE s FROM `spiele` as s LEFT JOIN turniere_liga as t on t.turnier_id = s.turnier_id WHERE t.turnier_id is NULL;
ALTER TABLE `spiele` ADD FOREIGN KEY (`team_id_a`) REFERENCES `teams_liga`(`team_id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `spiele` ADD FOREIGN KEY (`team_id_b`) REFERENCES `teams_liga`(`team_id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `spiele` ADD FOREIGN KEY (`schiri_team_id_a`) REFERENCES `teams_liga`(`team_id`) ON DELETE RESTRICT ON UPDATE RESTRICT; ALTER TABLE `spiele` ADD FOREIGN KEY (`schiri_team_id_b`) REFERENCES `teams_liga`(`team_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
ALTER TABLE `spiele` ADD FOREIGN KEY (`turnier_id`) REFERENCES `turniere_liga`(`turnier_id`) ON DELETE CASCADE ON UPDATE CASCADE;


DROP TABLE `spielplan_details`, `spielplan_paarungen`;


CREATE TABLE `spielplan_details` (
  `spielplan` varchar(30) NOT NULL,
  `spielplan_paarung` varchar(30) DEFAULT NULL,
  `plaetze` tinyint(4) DEFAULT NULL,
  `anzahl_halbzeiten` tinyint(4) DEFAULT NULL,
  `halbzeit_laenge` tinyint(4) DEFAULT NULL,
  `puffer` tinyint(4) DEFAULT NULL,
  `pausen` varchar(30) DEFAULT NULL COMMENT 'nach Spiel,Minuten#next',
  `faktor` tinyint(4) NOT NULL COMMENT 'Nur Nenner'
);

INSERT INTO `spielplan_details` (`spielplan`, `spielplan_paarung`, `plaetze`, `anzahl_halbzeiten`, `halbzeit_laenge`, `puffer`, `pausen`, `faktor`) VALUES
('4er_jgj_2_12_6', '4er_jgj_default', 4, 2, 12, 6, '2,12#4,12', 5),
('4er_jgj_default', '4er_jgj_default', 4, 2, 20, 5, '2,30#4,30', 5),
('5er_jgj_default', '5er_jgj_default', 5, 2, 15, 5, NULL, 5),
('6er_jgj_default', '6er_jgj_default', 6, 2, 12, 6, NULL, 6),
('7er_jgj_default', '7er_jgj_default', 7, 2, 9, 4, NULL, 7),
('8er_dko_default', NULL, 8, NULL, NULL, NULL, NULL, 8),
('8er_gruppe_default', NULL, 8, NULL, NULL, NULL, NULL, 8),
('8er_jgj_versetzt', '8er_jgj_versetzt', 8, 1, 15, 3, NULL, 8);

ALTER TABLE `spielplan_details`
  ADD PRIMARY KEY (`spielplan`),
  ADD KEY `spielplan_paarung` (`spielplan_paarung`);

CREATE TABLE `spielplan_paarungen` (
  `spielplan_paarung` varchar(30) NOT NULL,
  `spiel_id` tinyint(4) NOT NULL,
  `team_a` tinyint(4) NOT NULL,
  `team_b` tinyint(4) NOT NULL,
  `schiri_a` tinyint(4) NOT NULL,
  `schiri_b` tinyint(4) NOT NULL
);

INSERT INTO `spielplan_paarungen` (`spielplan_paarung`, `spiel_id`, `team_a`, `team_b`, `schiri_a`, `schiri_b`) VALUES
('4er_jgj_default', 1, 2, 3, 1, 4),
('4er_jgj_default', 2, 1, 4, 2, 3),
('4er_jgj_default', 3, 2, 4, 1, 3),
('4er_jgj_default', 4, 1, 3, 2, 4),
('4er_jgj_default', 5, 3, 4, 1, 2),
('4er_jgj_default', 6, 1, 2, 3, 4),
('5er_jgj_default', 1, 2, 5, 4, 1),
('5er_jgj_default', 2, 1, 4, 5, 2),
('5er_jgj_default', 3, 3, 5, 2, 1),
('5er_jgj_default', 4, 2, 4, 5, 3),
('5er_jgj_default', 5, 1, 3, 2, 4),
('5er_jgj_default', 6, 4, 5, 1, 3),
('5er_jgj_default', 7, 2, 3, 4, 5),
('5er_jgj_default', 8, 1, 5, 2, 3),
('5er_jgj_default', 9, 3, 4, 1, 5),
('5er_jgj_default', 10, 1, 2, 3, 4),
('6er_jgj_default', 1, 3, 6, 2, 1),
('6er_jgj_default', 2, 2, 5, 3, 6),
('6er_jgj_default', 3, 1, 4, 5, 2),
('6er_jgj_default', 4, 2, 3, 1, 4),
('6er_jgj_default', 5, 4, 6, 2, 3),
('6er_jgj_default', 6, 1, 5, 4, 6),
('6er_jgj_default', 7, 2, 4, 5, 1),
('6er_jgj_default', 8, 3, 5, 4, 2),
('6er_jgj_default', 9, 1, 6, 5, 3),
('6er_jgj_default', 10, 4, 5, 1, 6),
('6er_jgj_default', 11, 2, 6, 5, 4),
('6er_jgj_default', 12, 1, 3, 2, 6),
('6er_jgj_default', 13, 5, 6, 1, 3),
('6er_jgj_default', 14, 3, 4, 5, 6),
('6er_jgj_default', 15, 1, 2, 3, 4),
('7er_jgj_default', 1, 2, 7, 1, 4),
('7er_jgj_default', 2, 1, 4, 7, 2),
('7er_jgj_default', 3, 3, 6, 2, 1),
('7er_jgj_default', 4, 5, 7, 6, 3),
('7er_jgj_default', 5, 2, 4, 7, 5),
('7er_jgj_default', 6, 1, 6, 2, 4),
('7er_jgj_default', 7, 3, 5, 6, 1),
('7er_jgj_default', 8, 4, 7, 3, 5),
('7er_jgj_default', 9, 2, 6, 7, 4),
('7er_jgj_default', 10, 1, 5, 2, 6),
('7er_jgj_default', 11, 3, 7, 5, 1),
('7er_jgj_default', 12, 4, 6, 7, 3),
('7er_jgj_default', 13, 2, 5, 6, 4),
('7er_jgj_default', 14, 1, 3, 2, 5),
('7er_jgj_default', 15, 6, 7, 3, 1),
('7er_jgj_default', 16, 4, 5, 6, 7),
('7er_jgj_default', 17, 2, 3, 5, 4),
('7er_jgj_default', 18, 1, 7, 2, 3),
('7er_jgj_default', 19, 5, 6, 7, 1),
('7er_jgj_default', 20, 3, 4, 6, 5),
('7er_jgj_default', 21, 1, 2, 3, 4),
('8er_jgj_versetzt', 1, 6, 7, 5, 8),
('8er_jgj_versetzt', 2, 5, 8, 6, 7),
('8er_jgj_versetzt', 3, 5, 7, 5, 8),
('8er_jgj_versetzt', 4, 6, 8, 5, 7),
('8er_jgj_versetzt', 5, 5, 6, 6, 8),
('8er_jgj_versetzt', 6, 7, 8, 5, 6),
('8er_jgj_versetzt', 7, 3, 6, 7, 8),
('8er_jgj_versetzt', 8, 4, 5, 3, 6),
('8er_jgj_versetzt', 9, 1, 7, 4, 5),
('8er_jgj_versetzt', 10, 2, 8, 1, 7),
('8er_jgj_versetzt', 11, 4, 6, 2, 8),
('8er_jgj_versetzt', 12, 3, 5, 4, 6),
('8er_jgj_versetzt', 13, 2, 7, 3, 5),
('8er_jgj_versetzt', 14, 1, 6, 2, 7),
('8er_jgj_versetzt', 15, 3, 8, 1, 6),
('8er_jgj_versetzt', 16, 2, 5, 3, 8),
('8er_jgj_versetzt', 17, 4, 7, 2, 5),
('8er_jgj_versetzt', 18, 1, 8, 4, 7),
('8er_jgj_versetzt', 19, 2, 6, 1, 8),
('8er_jgj_versetzt', 20, 3, 7, 2, 6),
('8er_jgj_versetzt', 21, 1, 5, 3, 7),
('8er_jgj_versetzt', 22, 4, 8, 1, 5),
('8er_jgj_versetzt', 23, 2, 3, 4, 8),
('8er_jgj_versetzt', 24, 1, 4, 2, 3),
('8er_jgj_versetzt', 25, 2, 4, 1, 4),
('8er_jgj_versetzt', 26, 1, 3, 2, 4),
('8er_jgj_versetzt', 27, 3, 4, 1, 3),
('8er_jgj_versetzt', 28, 1, 2, 3, 4);

ALTER TABLE `spielplan_paarungen`
  ADD PRIMARY KEY (`spielplan_paarung`,`spiel_id`);

ALTER TABLE `spielplan_details`
  ADD CONSTRAINT `spielplan_details_ibfk_1` FOREIGN KEY (`spielplan_paarung`) REFERENCES `spielplan_paarungen` (`spielplan_paarung`);

ALTER TABLE `mailbot` CHANGE `inhalt` `inhalt` VARCHAR(2000) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;

ALTER TABLE `turniere_details` ADD `set_spielplan` VARCHAR(30) NULL AFTER `startgebuehr`;

ALTER TABLE `turniere_details` CHANGE `spielplan` `format` VARCHAR(255) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE `turniere_liga` ADD `spielplan_vorlage` VARCHAR(30) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL AFTER `phase`, ADD `spielplan_datei` VARCHAR(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL AFTER `spielplan_vorlage`;

UPDATE turniere_liga as tl
INNER JOIN turniere_details as td on td.turnier_id = tl.turnier_id
SET tl.spielplan_vorlage = td.set_spielplan, tl.spielplan_datei = td.link_spielplan
WHERE tl.turnier_id = td.turnier_id;

ALTER TABLE `turniere_liga` ADD FOREIGN KEY (`spielplan_vorlage`) REFERENCES `spielplan_details`(`spielplan`) ON DELETE RESTRICT ON UPDATE RESTRICT;

ALTER TABLE `turniere_details`
  DROP `set_spielplan`,
  DROP `link_spielplan`;

UPDATE `turniere_liga` SET `spielplan_datei`= NULL WHERE spielplan_datei = '';