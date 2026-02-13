ALTER TABLE `neuigkeiten`
    ADD `art` ENUM ('neuigkeit','foerdermittel') NOT NULL DEFAULT 'neuigkeit' AFTER `aktiv`;

CREATE TABLE IF NOT EXISTS `db_localhost`.`spieler_statistik`
(
    `id`         INT      NOT NULL AUTO_INCREMENT,
    `date`       DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    `saison`     INT      NOT NULL,
    `geschlecht` ENUM ('m', 'w', 'd'),
    `anzahl`     INT      NOT NULL,
    PRIMARY KEY (`id`)
);