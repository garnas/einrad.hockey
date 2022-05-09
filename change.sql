UPDATE `turniere_details` SET `plz`= LPAD(plz, 5, '0');
ALTER TABLE `turniere_details` CHANGE `plz` `plz` VARCHAR(6) NULL DEFAULT NULL;
