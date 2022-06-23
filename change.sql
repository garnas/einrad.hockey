ALTER TABLE `turniere_liste` CHANGE `liste` `liste` VARCHAR(80) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL DEFAULT '\'warte\'';
ALTER TABLE `turniere_liga` ADD `canceled` INT(1) NOT NULL DEFAULT '0' AFTER `saison`;
ALTER TABLE `turniere_liga` ADD `canceled_grund` VARCHAR(255) NULL AFTER `canceled`;
ALTER TABLE `turniere_liga` CHANGE `phase` `phase` VARCHAR(50) CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NULL DEFAULT NULL;

