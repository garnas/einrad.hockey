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