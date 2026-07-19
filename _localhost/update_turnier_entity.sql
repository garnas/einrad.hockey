ALTER TABLE `turniere_liga` CHANGE `sofort_oeffnen` `sofort_oeffnen_frei` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `turniere_liga` CHANGE `saison` `saison` int(11) DEFAULT NULL AFTER `spielplan_datei`, CHANGE `canceled` `canceled` int(11) NOT NULL DEFAULT 0 AFTER `saison`, CHANGE `canceled_grund` `canceled_grund` varchar(255) DEFAULT NULL AFTER `canceled`, CHANGE `erstellt_am` `erstellt_am` timestamp NULL DEFAULT NULL AFTER `canceled_grund`;

ALTER TABLE `turniere_liga` ADD `block_erweitert_frei` TINYINT(1) NOT NULL DEFAULT '0' AFTER `sofort_oeffnen_frei`;
UPDATE `turniere_liga` SET `block_erweitert_frei` = 1 WHERE `tblock` LIKE "ABCDEF";

ALTER TABLE `turniere_liga` ADD `sofort_oeffnen_hoch` TINYINT(1) NOT NULL DEFAULT '0' AFTER `sofort_oeffnen_frei`;
ALTER TABLE `turniere_liga` ADD `sofort_oeffnen_runter` TINYINT(1) NOT NULL DEFAULT '0' AFTER `sofort_oeffnen_hoch`;

UPDATE `turniere_liga` SET `block_erweitert_runter` = 0 WHERE `block_erweitert_runter` IS NULL;
UPDATE `turniere_liga` SET `block_erweitert_hoch` = 0 WHERE `block_erweitert_hoch` IS NULL;

ALTER TABLE `turniere_liga` CHANGE `block_erweitert_hoch` `block_erweitert_hoch` TINYINT(1) NOT NULL DEFAULT '0';
ALTER TABLE `turniere_liga` CHANGE `block_erweitert_runter` `block_erweitert_runter` TINYINT(1) NOT NULL DEFAULT '0';