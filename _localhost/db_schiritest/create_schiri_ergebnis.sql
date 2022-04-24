DROP TABLE IF EXISTS schiri_ergebnis;

CREATE TABLE schiri_ergebnis (
    `schiri_test_id`      INT NOT NULL AUTO_INCREMENT,
    `md5sum`              VARCHAR(32) NULL,
    `spieler_id`          INT NOT NULL,
    `spieler_email`       VARCHAR(500) NULL,
    `gestellte_fragen`    VARCHAR(500) NULL,
    `gesetzte_antworten`  VARCHAR(500) NULL,
    `test_level`          ENUM('J','B','F') NULL,
    `bestanden`           ENUM('Ja','Nein') NULL,
    `kommentar`           VARCHAR(500) NULL,
    `t_erstellt`          DATETIME NULL,
    `t_gestartet`         DATETIME NULL,
    `t_abgegeben`         DATETIME NULL,
    `saison`              INT NOT NULL,
    `schiri_test_version` INT NOT NULL,
    PRIMARY KEY (`schiri_test_id`))

