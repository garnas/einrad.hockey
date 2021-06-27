<?php

/**
 * Class LigaBot
 */
class LigaBot
{

    /**
     * Führt den Ligabot aus
     */
    public static function liga_bot()
    {

        db::sql_backup(); // Datenbank wird gesichert

        $doppel_anmeldungen = self::get_doppel_anmeldungen(); // Doppelt auf Spiele-Liste wird hier erfasst.
        if (!empty($doppel_anmeldungen)) {
            Html::notice("Doppelt auf Spielen-Liste:");
            db::debug($doppel_anmeldungen); // TODO Mail absenden?
        }

        $_SESSION['logins']['ligabot'] = 'Ligabot'; // Wird in den logs als Autor verwendet

        self::set_spieltage(); // Setzt alle Spieltage der Turniere

        $liste = self::get_turnier_ids(); // Liste aller relevanten Turnierids sortiert nach Turnierdatum
        foreach ($liste as $turnier_id) { // Schleife durch alle Turniere
            $turnier = new Turnier ($turnier_id);
            /**
             * Turnierblock wandern lassen
             */
            $ausrichter_block = Tabelle::get_team_block($turnier->details['ausrichter']);
            $turnier_block = $turnier->details['tblock'];
            // Position des Ausrichters in einem Array aller Blöcke in der Klasse Config, um Blockhöhere und erweiterte
            // Turniere erkennen zu können
            $pos_ausrichter = array_search($ausrichter_block, Config::BLOCK_ALL);
            $pos_turnier = array_search($turnier_block, Config::BLOCK_ALL);

            if (
                $turnier->details['art'] === 'I'
                && $turnier->details['phase'] === 'offen'
                && $turnier->details['tblock_fixed'] !== 'Ja'
                && $turnier_block != $ausrichter_block
                && ($pos_ausrichter - 1) != $pos_turnier
            ) { // Um einen Block vom Ausrichterblock aus erweiterte Turniere sollen nicht wandern...
                $turnier->set_liga('tblock', $ausrichter_block);
            }
            if (
                $turnier->details['art'] === 'II'
                && $turnier->details['tblock_fixed'] !== 'Ja'
                && $turnier->details['phase'] === 'offen'
                && $pos_ausrichter < $pos_turnier
            ) {
                $turnier->set_liga('tblock', $ausrichter_block);
            }

            /**
             * Phasenwechsel von Offene Phase in die Meldephase
             */
            // Prüft, ob wir uns vier Wochen vor dem Spieltag befinden und ob das Turnier in der offenen Phase ist.
            if (
                $turnier->details['phase'] === 'offen'
                && self::time_offen_melde($turnier->details['datum']) <= time()
            ) {
                $turnier->set_liga('phase', 'melde'); //Aktualisiert auch $turnier->get_details()
                // Losen setzt alle Teams in richtiger Reihenfolge auf die Warteliste.
                self::losen($turnier);
                // Füllt die Spielen-Liste auf.
                $turnier->spieleliste_auffuellen(false);
                // Info-Mails versenden.
                MailBot::mail_gelost($turnier);
                // Freie-Plätze-Mails versenden.
                MailBot::mail_plaetze_frei($turnier);
            }
        } //end foreach
        unset($_SESSION['logins']['ligabot']);
        Html::info("Ligabot erfolgreich ausgeführt");
    }

    /**
     * Geht alle Turniere durch und schreibt den jeweiligen Spieltag in die Datenbank
     */
    public static function set_spieltage(): void
    {
        $liste = self::get_turnier_ids(); // Liste aller relevanten Turnierids sortiert nach Turnierdatum

        // Initierung
        $spieltag = 0;
        $kw = 0; // Kalenderwoche als Hilfsmittel um Spieltage zu bestimmen

        /**
         * Spieltag identifizieren und in die Tabelle schreiben.
         */
        foreach ($liste as $turnier_id) { // Schleife durch alle Turniere
            $turnier = new Turnier ($turnier_id);
            $datum = strtotime($turnier->details['datum']); // Datum des Turniers als Unix-Time
            $wochentag = date("N", $datum); // Wochentage nummeriert 1 - 7
            // Man muss auch Turniere berücksichtigen, welche nicht am Wochende sind:
            if ($kw != date('W', $datum)) {
                ++$spieltag;
            }
            $set_spieltag = $spieltag;
            // Turniere die Mo oder Di stattfinden werden dem vorherigen Spieltag zugeordnet
            if (($wochentag < 3) && (date('W', $datum) - $kw) <= 1) {
                if ($spieltag === 0) { // Ansonsten würde Spieltag 0 vergeben, wenn das erste Turnier nicht an einem WE stattfindet
                    $spieltag = 1;
                }
                $set_spieltag = $spieltag - 1;
            }
            if ($turnier->details['spieltag'] != $set_spieltag) { // Die Datenbank wird nur beschrieben, wenn sich der Spieltag ändert.
                $turnier->set_liga('spieltag', $set_spieltag);
            }
            $kw = date('W', $datum); // Kalenderwoche übernehmen für die nächste Iteration
        }

        Team::set_schiri_freilose();
    }

    /**
     * Get alle TurnierIDs um sie im Ligabot abzuarbeiten
     *
     * @param int $saison
     * @return array
     */
    public static function get_turnier_ids(int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turnier_id 
                FROM turniere_liga 
                WHERE saison = ? 
                AND (art='I' OR art='II' OR art='III') 
                ORDER BY datum
                ";
        return db::$db->query($sql, $saison)->esc()->list('turnier_id');
    }


    /**
     * Ermittelt den Zeitpunkt, wann das Turnier in die Meldephase wechseln soll und gibt diesen Zeitpunk als Unix-Time zurück
     *
     * @param string $datum mit strtotime lesbares Datum
     * @return int
     */
    public static function time_offen_melde(string $datum): int
    {
        $unix = strtotime($datum);
        $tag = date("N", $unix); // Numerische Zahl des Wochentages 1-7
        // Faktor 3.93 und strtotime(date("d-M-Y"..)) -> Reset von 12 Uhr Mittags auf Null Uhr, um Winter <-> Sommerzeit korrekt handzuhaben
        if ($tag >= 3) {
            return strtotime(date("d-M-Y", $unix - 3.93 * 7 * 24 * 60 * 60 + (6 - $tag) * 24 * 60 * 60));
        }
        return strtotime(date("d-M-Y", $unix - 3.93 * 7 * 24 * 60 * 60 - $tag * 24 * 60 * 60));
    }

    /**
     * Findet Doppelanmeldungen
     *
     * @return array
     */
    public static function get_doppel_anmeldungen(): array
    {
        $sql = "
                SELECT turniere_liste.team_id, teams_liga.ligateam, COUNT(*), turniere_liga.datum 
                FROM turniere_liste 
                INNER JOIN turniere_liga 
                ON turniere_liste.turnier_id = turniere_liga.turnier_id 
                LEFT JOIN teams_liga 
                ON teams_liga.team_id = turniere_liste.team_id 
                WHERE turniere_liste.liste = 'spiele' 
                AND teams_liga.ligateam = 'Ja' 
                GROUP BY turniere_liga.datum, turniere_liste.team_id 
                HAVING (COUNT(*) > 1)
                ";
        return db::$db->query($sql)->esc()->fetch();
    }

    /**
     * Regelt den Übergang von offen zu melden bezüglich der Teamlisten.
     * setzt die Teams in geloster Reihenfolge auf die Warteliste, also danach: Spielen-Liste auffuellen!
     * @param Turnier $turnier
     * Objekt des Typs Turnier
     *
     * @return bool
     */
    public static function losen(Turnier $turnier): bool
    {
        // Falsche Freilosanmeldungen beim Übergang in die Meldephase abmelden
        Html::info($turnier->id . " wurde gelost.");
        $liste = $turnier->get_anmeldungen();
        foreach (($liste['spiele'] ?? []) as $team) {
            // Das Team hat ein Freilos gesetzt, aber den falschen Freilosblock
            if ($team['freilos_gesetzt'] === 'Ja' && !$turnier->check_team_block_freilos($team['team_id'])) {
                $turnier->log("Falscher Freilos-Block: " . $team['teamname']
                    . "\r\nTeamb. " . Tabelle::get_team_block($team['team_id']) . " | Turnierb. " . $turnier->details['tblock']
                    . "\r\nFreilos wird erstattet");
                $turnier->set_liste($team['team_id'], 'warte');
                Team::add_freilos($team['team_id']);
                MailBot::mail_freilos_abmeldung($turnier, $team['team_id']);
                // Anmeldeliste aktualisieren
                $liste = $turnier->get_anmeldungen();
            }
        }

        $anz_spiele = count($liste['spiele']);
        $anz_warte = count($liste['warte']);
        $anz_melde = count($liste['melde']);

        // Anzahl der zu losenden Teams
        $anz_los = $anz_warte + $anz_melde + $anz_spiele - $turnier->details['plaetze'];
        if ($anz_los < 0) {
            $gelost = true;
            $turnier->log("Turnierplätze werden verlost");
        }

        $los_nl = $los_rblock = $los_fblock = [];   // 3 Lostöpfe für Nichtligateams, Teams mit richtigem Block und
                                                    // Teams mit falschem Block
        // Aufteilung der Teams in die Lostöpf, Teams mit falschem Freilos wurden schon abgemeldet
        foreach ($liste['melde'] as $team) {
            if ($team['ligateam'] === 'Nein') {
                $los_nl[] = $team['team_id'];
            } elseif ($turnier->check_team_block($team['team_id'])) {
                $los_rblock[] = $team['team_id'];
            } else {
                $los_fblock[] = $team['team_id'];
            }
        }

        // Losen durch "mischen" der Losttöpfe
        shuffle($los_rblock);
        shuffle($los_fblock);
        shuffle($los_nl);

        // Zusammenstellen der neuen Warteliste
        $los_ges = array_merge($los_rblock, $los_nl, $los_fblock);
        $pos = 0;
        foreach ($los_ges as $team_id) {
            $pos++;
            if ($turnier->check_doppel_anmeldung($team_id)) { //Check ob das Team am Kalendertag des Turnieres schon auf einer Spiele-Liste steht
                $turnier->log("Doppelanmeldung " . Team::id_to_name($team_id));
                $turnier->abmelden($team_id);
                Html::info("Abmeldung Doppelanmeldung im Turnier" . $turnier->id . ": \r\n" . Team::id_to_name($team_id));
            } else {
                $turnier->set_liste($team_id, 'warte', $pos);
            }
        }
        // NACH Zusammenstellen der Warteliste via losen, muss die Spielen-Liste über spieleliste_auffuellen aufgefuellt werden!!
        return $gelost ?? false;
    }

    // Alle I,II,III Turniere werden in die Offene Phase geschickt
    // Fürs Debugging
    /*
    public static function zuruecksetzen()
    {
        $liste = self::get_turnier_ids();
        foreach ($liste as $turnier_id){
            $akt_turnier = new Turnier ($turnier_id);
            if ($akt_turnier->get_details['phase'] != 'ergebnis'){
                $akt_turnier -> set_phase("offen");
                $akt_turnier -> set_spieltag(0);
                $akt_turnier -> log("Phase -> offen" , "Zurückgesetzt von Ligabot");
                $akt_turnier -> log("Spieltag -> 0" , "Zurückgesetzt von Ligabot");
            }
        }
    }
    */
}