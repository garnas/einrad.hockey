CREATE TABLE `db_localhost`.`freilose`
(
    `freilos_id`  INT          NOT NULL AUTO_INCREMENT,
    `gesetzt_am`  DATETIME     NULL,
    `erstellt_am` DATETIME     NOT NULL,
    `team_id`     INT          NOT NULL,
    `turnier_id`  INT          NULL,
    `grund`       VARCHAR(255) NOT NULL,
    PRIMARY KEY (`freilos_id`)
) ENGINE = InnoDB;

ALTER TABLE `freilose`
    ADD FOREIGN KEY (`team_id`) REFERENCES `teams_liga` (`team_id`) ON DELETE CASCADE ON UPDATE CASCADE;
ALTER TABLE `freilose`
    ADD FOREIGN KEY (`turnier_id`) REFERENCES `turniere_liga` (`turnier_id`) ON DELETE CASCADE ON UPDATE CASCADE;

ALTER TABLE `freilose` CHANGE `grund` `grund` ENUM('SCHIRI','TURNIER_AUSGERICHTET','FREILOS_GESETZT','SONSTIGES') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
ALTER TABLE `freilose` ADD `saison` INT NOT NULL AFTER `grund`;

DELETE FROM ligaleitung WHERE `ligaleitung`.`ligaleitung_id` = 7;

ALTER TABLE `ligaleitung` CHANGE `funktion` `funktion` ENUM('ligaausschuss','schiriausschuss','oeffentlichkeitsausschuss','technikausschuss','schiriausbilder','admin') CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL;
UPDATE `ligaleitung` SET `funktion` = 'admin' WHERE `ligaleitung`.`ligaleitung_id` = 9;

ALTER TABLE `turniere_details` ADD `min_teams` INT NULL AFTER `startgebuehr`;
