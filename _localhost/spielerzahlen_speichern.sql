CREATE TABLE IF NOT EXISTS `db_localhost`.`spieler_statistik` (
    `id` INT NOT NULL AUTO_INCREMENT , 
    `date` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP , 
    `saison` INT NOT NULL , 
    `geschlecht` ENUM('m', 'w', 'd') ,
    `anzahl` INT NOT NULL , 
    PRIMARY KEY (`id`)
); 

-- Insert für vergangene Saisons 
-- INSERT INTO `spieler_statistik` (`id`, `date`, `saison`, `geschlecht`, `anzahl`) VALUES 
--     (NULL, '2025-05-31 23:59:59', 30, 'm', 324), 
--     (NULL, '2025-05-31 23:59:59', 30, 'w', 345),
--     (NULL, '2025-05-31 23:59:59', 30, 'd', 2),
--     (NULL, '2025-05-31 23:59:59', 30, NULL, 0)

-- Insert für die aktuelle Saison
INSERT INTO `spieler_statistik` (`id`, `date`, `saison`, `geschlecht`, `anzahl`)
SELECT NULL, current_timestamp() as date, max(letzte_saison) as saison, geschlecht, count(*) as anzahl
FROM spieler
WHERE letzte_saison >= 31
AND team_id IS NOT NULL
GROUP BY geschlecht
;