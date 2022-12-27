-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `abstimmung_ergebnisse`
--

CREATE TABLE `abstimmung_ergebnisse` (
                                         `crypt` varchar(255) NOT NULL,
                                         `stimme` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `abstimmung_teams`
--

CREATE TABLE `abstimmung_teams` (
                                    `team_id` int(11) NOT NULL,
                                    `aenderungen` int(8) NOT NULL DEFAULT 0,
                                    `passwort` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

ALTER TABLE `abstimmung_teams`
    ADD PRIMARY KEY(
                    `team_id`
        );