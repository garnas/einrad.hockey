DELETE FROM ligaleitung WHERE login IS NULL AND `funktion` NOT LIKE 'schiriausbilder';

ALTER TABLE `ligaleitung` 
CHANGE `funktion` `funktion` VARCHAR(255)
CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci
NOT NULL;

INSERT INTO `ligaleitung` (`ligaleitung_id`, `spieler_id`, `funktion`, `email`, `login`, `passwort`) VALUES 
(NULL, '64', 'team_schiripruefer', NULL, NULL, NULL),
(NULL, '2262', 'team_schiripruefer', NULL, NULL, NULL),
(NULL, '191', 'team_schiripruefer', NULL, NULL, NULL),
(NULL, '168', 'team_schiripruefer', NULL, NULL, NULL),
(NULL, '297', 'team_schiripruefer', NULL, NULL, NULL);

INSERT INTO `ligaleitung` (`ligaleitung_id`, `spieler_id`, `funktion`, `email`, `login`, `passwort`) VALUES 
(NULL, '2009', 'team_jugendarbeit', NULL, NULL, NULL),
(NULL, '2867', 'team_jugendarbeit', NULL, NULL, NULL),
(NULL, '64', 'team_jugendarbeit', NULL, NULL, NULL),
(NULL, '297', 'team_jugendarbeit', NULL, NULL, NULL),
(NULL, '379', 'team_jugendarbeit', NULL, NULL, NULL);

INSERT INTO `ligaleitung` (`ligaleitung_id`, `spieler_id`, `funktion`, `email`, `login`, `passwort`) VALUES 
(NULL, '874', 'team_technik', NULL, NULL, NULL),
(NULL, '2398', 'team_technik', NULL, NULL, NULL),
(NULL, '240', 'team_technik', NULL, NULL, NULL),
(NULL, '2865', 'team_technik', NULL, NULL, NULL),
(NULL, '1251', 'team_technik', NULL, NULL, NULL);

UPDATE `ligaleitung` SET `funktion` = 'team_social_media' WHERE `ligaleitung`.`ligaleitung_id` = 14;
INSERT INTO `ligaleitung` (`ligaleitung_id`, `spieler_id`, `funktion`, `email`, `login`, `passwort`) VALUES 
(NULL, '2867', 'team_social_media', NULL, NULL, NULL),
(NULL, '2262', 'team_social_media', NULL, NULL, NULL);

INSERT INTO `ligaleitung` (`ligaleitung_id`, `spieler_id`, `funktion`, `email`, `login`, `passwort`) VALUES 
(NULL, '1573', 'team_schirileitfaden', NULL, NULL, NULL),
(NULL, '2163', 'team_schirileitfaden', NULL, NULL, NULL),
(NULL, '120', 'team_schirileitfaden', NULL, NULL, NULL);

INSERT INTO `ligaleitung` (`ligaleitung_id`, `spieler_id`, `funktion`, `email`, `login`, `passwort`) VALUES 
(NULL, '600', 'team_branding_merch', NULL, NULL, NULL),
(NULL, '874', 'team_branding_merch', NULL, NULL, NULL),
(NULL, '153', 'team_branding_merch', NULL, NULL, NULL),
(NULL, '2398', 'team_branding_merch', NULL, NULL, NULL),
(NULL, '', 'team_branding_merch', NULL, NULL, NULL);