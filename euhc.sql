INSERT INTO `spielplan_details` (`spielplan`, `spielplan_paarung`, `plaetze`, `anzahl_halbzeiten`, `halbzeit_laenge`, `puffer`, `pausen`, `faktor`)
VALUES ('euhc_b', '4er_jgj_default', '9', '2', '10', '8', NULL, '1');
UPDATE `spielplan_details` SET `pausen` = '16,842' WHERE `spielplan_details`.`spielplan` = 'euhc_b';

INSERT INTO `spielplan_details` (`spielplan`, `spielplan_paarung`, `plaetze`, `anzahl_halbzeiten`, `halbzeit_laenge`, `puffer`, `pausen`, `faktor`)
VALUES ('euhc_a', '4er_jgj_default', '8', '2', '12', '6', NULL, '1');
# UPDATE `spielplan_details` SET `pausen` = '16,784' WHERE `spielplan_details`.`spielplan` = 'euhc_b';

DELETE FROM spiele WHERE turnier_id = 0;
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '1', '1', '2', '5', '7', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '2', '3', '4', '1', '2', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '3', '5', '6', '3', '4', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '4', '7', '8', '5', '6', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '5', '3', '9', '7', '8', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '6', '5', '2', '3', '9', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '7', '7', '4', '5', '2', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '8', '8', '6', '7', '4', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '9', '5', '1', '8', '6', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '10', '7', '9', '5', '1', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '11', '8', '2', '7', '9', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '12', '6', '4', '8', '2', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '13', '7', '3', '6', '4', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '14', '8', '1', '7', '3', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '15', '6', '9', '8', '1', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '16', '4', '2', '6', '9', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '17', '8', '5', '4', '2', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '18', '6', '3', '8', '5', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '19', '4', '1', '6', '3', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '20', '2', '9', '4', '1', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '21', '6', '7', '2', '9', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '22', '4', '5', '6', '7', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '23', '2', '3', '4', '5', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '24', '9', '1', '2', '3', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '25', '4', '8', '9', '1', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '26', '2', '7', '4', '8', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '27', '9', '5', '2', '7', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '28', '1', '3', '9', '5', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '29', '2', '6', '1', '3', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '30', '9', '8', '2', '6', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '31', '1', '7', '9', '8', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '32', '3', '5', '1', '7', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '33', '9', '4', '3', '5', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '34', '1', '6', '9', '4', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '35', '3', '8', '1', '6', NULL, NULL, NULL, NULL);
INSERT INTO spiele (turnier_id, spiel_id, team_id_a, team_id_b, schiri_team_id_a, schiri_team_id_b, tore_a, tore_b, penalty_a, penalty_b) VALUES ('0', '36', '5', '7', '3', '8', NULL, NULL, NULL, NULL);


INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '37', '2', '8', '5', '6', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '38', '3', '6', '2', '8', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '39', '1', '5', '3', '6', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '40', '4', '7', '1', '5', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '41', '2', '6', '4', '7', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '42', '3', '7', '2', '6', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '43', '1', '8', '3', '7', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '44', '4', '5', '1', '8', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '45', '2', '7', '4', '5', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '46', '3', '5', '2', '7', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '47', '1', '6', '3', '5', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '48', '4', '8', '1', '6', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '49', '2', '3', '4', '8', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '50', '1', '4', '2', '3', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '51', '5', '8', '1', '4', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '52', '7', '6', '5', '8', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '53', '1', '2', '7', '6', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '54', '3', '4', '1', '2', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '55', '5', '7', '3', '4', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '56', '6', '8', '5', '7', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '57', '1', '7', '6', '8', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '58', '2', '5', '1', '7', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '59', '3', '8', '2', '5', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '60', '4', '6', '3', '8', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '61', '7', '8', '4', '6', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '62', '1', '3', '7', '8', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '63', '2', '4', '1', '3', NULL, NULL, NULL, NULL);
INSERT INTO `spiele` (`turnier_id`, `spiel_id`, `team_id_a`, `team_id_b`, `schiri_team_id_a`, `schiri_team_id_b`, `tore_a`, `tore_b`, `penalty_a`, `penalty_b`) VALUES ('1', '64', '5', '6', '2', '4', NULL, NULL, NULL, NULL);