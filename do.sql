UPDATE `spieler` SET `schiri`='27' WHERE schiri = 'Ausbilder/in';
ALTER TABLE `spieler` CHANGE `schiri` `schiri` INT(6) NULL DEFAULT NULL;
UPDATE `spieler` SET `schiri`= NULL WHERE schiri = 0;
UPDATE `spieler` SET `schiri`= 27 WHERE schiri = 26;
ALTER TABLE `spieler` CHANGE `zeit` `timestamp` TIMESTAMP NULL;
ALTER TABLE `spieler` CHANGE `junior` `junior` ENUM('Ja','Nein','') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT 'Nein';
UPDATE `spieler` SET `junior`= NULL WHERE junior != 'Ja';
ALTER TABLE `spieler` CHANGE `junior` `junior` ENUM('Ja') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

