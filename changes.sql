ALTER TABLE `freilose` ADD `ausgerichtete_turnier_id` INT(16) NULL AFTER `grund`;
ALTER TABLE `freilose` ADD FOREIGN KEY (`ausgerichtete_turnier_id`) REFERENCES `turniere_liga`(`turnier_id`) ON DELETE RESTRICT ON UPDATE RESTRICT;
