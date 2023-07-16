UPDATE `teams_liga` SET `freilose`=0,`zweites_freilos`= NULL WHERE aktiv = 'Ja';
DELETE FROM `spieler` WHERE letzte_saison < 27;
DELETE FROM `mailbot` WHERE mail_status != 'warte';
# DELETE FROM teams_liga WHERE teams_liga.team_id IN (..)
UPDATE `turniere_liga` SET `saison` = '29' WHERE `turniere_liga`.`turnier_id` = 1109;
UPDATE `turniere_liga` SET `saison` = '29' WHERE `turniere_liga`.`turnier_id` = 1108;
