DROP TABLE IF EXISTS schiri_ergebnis;

CREATE TABLE schiri_ergebnis (
    `schiri_test_id`      INT NOT NULL AUTO_INCREMENT,
    `schiri_test_md5`     VARCHAR(32) NULL,
    `spieler_id`          INT NOT NULL,
    `gestellte_fragen`    VARCHAR(500) NULL,
    `gesetzte_antworten`  VARCHAR(500) NULL,
    `test_level`          ENUM('J','B','F') NULL,
    `bestanden`           ENUM('Ja','Nein') NULL,
    `kommentar`           VARCHAR(500) NULL,
    `time_stamp`          DATETIME NULL,
    `saison`              INT NOT NULL,
    `schiri_test_version` INT NOT NULL,
    PRIMARY KEY (`schiri_test_id`))

