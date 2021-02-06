<?php

/**
 * Class Turnier
 *
 * Alles für die Verwaltung und zum Anzeigen von Turnieren
 */
class Turnier
{

    /**
     * Einrdeutige TurnierID
     * @var int
     */
    public int $turnier_id;
    /**
     * Array aller Turnierdaten
     * @var array
     */
    public array $details;

    /**
     * Turnier constructor.
     * @param $turnier_id
     */
    function __construct($turnier_id)
    {
        $this->turnier_id = $turnier_id;
        $this->details = $this->get_turnier_details();
    }

    /**
     * Erstellt ein neues Turnier in die Datenbank
     *
     * @param string $tname
     * @param string $ausrichter
     * @param string $startzeit
     * @param string $besprechung
     * @param string $art
     * @param string $tblock
     * @param string $fixed
     * @param string $datum
     * @param string $plaetze
     * @param string $spielplan
     * @param string $hallenname
     * @param string $strasse
     * @param string $plz
     * @param string $ort
     * @param string $haltestellen
     * @param string $hinweis
     * @param string $startgebuehr
     * @param string $name
     * @param string $handy
     * @param string $phase
     * @return bool
     */
    public static function create_turnier(string $tname, string $ausrichter, string $startzeit, string $besprechung,
                                          string $art, string $tblock, string $fixed, string $datum, string $plaetze,
                                          string $spielplan, string $hallenname, string $strasse, string $plz,
                                          string $ort, string $haltestellen, string $hinweis, string $startgebuehr,
                                          string $name, string $handy, string $phase): bool
    {
        $saison = Config::SAISON;
        $turnier_id = db::get_auto_increment('turniere_liga');
        $sql = "
                INSERT INTO turniere_liga (tname, ausrichter, art, tblock, tblock_fixed, datum, phase, saison) 
                VALUES ('$tname','$ausrichter','$art','$tblock', '$fixed', '$datum', '$phase', '$saison')
                ";
        db::write($sql);
        $sql = "
                INSERT INTO turniere_details (turnier_id, hallenname, strasse, plz, ort, haltestellen, plaetze,
                spielplan, startzeit, besprechung, hinweis, organisator, handy, startgebuehr)
                VALUES ('$turnier_id','$hallenname','$strasse','$plz','$ort','$haltestellen','$plaetze','$spielplan',
                '$startzeit','$besprechung','$hinweis', '$name', '$handy', '$startgebuehr');
                ";
        db::write($sql);
        // Anmeldung des Ausrichters auf die Spielen-Liste
        $sql = "
                INSERT INTO turniere_liste (turnier_id, team_id, liste, freilos_gesetzt) 
                VALUES ('$turnier_id','$ausrichter','spiele','Nein')
                ";
        db::write($sql);

        // Spieltag in Abhängigkeit aller anderen Turniere bestimmen zu bestimmen
        Ligabot::set_spieltage();
        return true;
    }

    /********************
     * Get Turnierdaten
     *******************/

    /**
     * Alle Turnierdaten auf einmal um die Datenbank zu schonen
     * Format: $return[turnier_id][daten]
     *
     * Where-Klause für SQl-Query
     * @param string $where
     *
     * Sortierung
     * @param string $asc
     * @return array
     */
    public static function get_all_turniere($where = '', $asc = 'asc'): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                " . $where . "
                ORDER BY turniere_liga.datum $asc
                ";
        $result = db::read($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            $return[$x['turnier_id']] = $x;
        }
        return db::escape($return ?? []);
    }

    /**
     * Turnierdetails von nur einem Turnier erhalten
     *
     * @return array
     */
    function get_turnier_details(): array
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON turniere_liga.ausrichter = teams_liga.team_id
                WHERE turniere_liga.turnier_id = '$turnier_id'
                ";
        $result = db::read($sql);
        $return = mysqli_fetch_assoc($result);
        return db::escape($return ?? []);
    }

    /**
     * Alle Turnieranmeldelisten auf einmal um die Datenbank zu schonen
     * $turnierlisten[turnier_id][liste] => Array der Anmeldedaten
     *
     * @param int $saison
     * @return array
     */
    public static function get_all_anmeldungen($saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liste.*, teams_liga.teamname, teams_liga.ligateam
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                INNER JOIN turniere_liga
                ON turniere_liga.turnier_id = turniere_liste.turnier_id
                WHERE turniere_liga.saison = '$saison'
                AND turniere_liga.phase != 'ergebnis'
                ORDER BY turniere_liste.position_warteliste
                ";
        $result = db::read($sql);
        $turnier_listen = [];
        $spieltag = Tabelle::get_aktuellen_spieltag();
        while ($anmeldung = mysqli_fetch_assoc($result)) {
            if (empty($turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']])) {
                $turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']] = []; //Damit diverse Arrayfunktionen ordentlich funktionieren, auch bei leeren Listen.
            }
            $turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']][$anmeldung['team_id']] = $anmeldung;
            $turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']][$anmeldung['team_id']]['tblock'] = Tabelle::get_team_block($anmeldung['team_id'], $spieltag);
            $turnier_listen[$anmeldung['turnier_id']][$anmeldung['liste']][$anmeldung['team_id']]['wertigkeit'] = Tabelle::get_team_wertigkeit($anmeldung['team_id'], $spieltag);
        }
        return db::escape($turnier_listen); //array
    }

    /**
     * Anmeldungen der Warte-, Melde-, Spielen-Liste eines Turnieres
     * Format: zb. $liste['liste'][Array der Anmeldedaten der Angemeldeten Teams auf der entsprechenden Liste]
     * @return array
     */
    function get_anmeldungen(): array
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                SELECT turniere_liste.*, teams_liga.teamname, teams_liga.ligateam
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                WHERE turniere_liste.turnier_id='$turnier_id'
                ORDER BY turniere_liste.position_warteliste
                ";
        $result = db::read($sql);
        $liste = [];
        $liste['team_ids'] = $liste['teamnamen'] = $liste['spiele'] = $liste['melde'] = $liste['warte'] = [];
        while ($anmeldung = mysqli_fetch_assoc($result)) {
            //Für Turnierlisten
            $liste[$anmeldung['liste']][$anmeldung['team_id']] = $anmeldung;
            $liste[$anmeldung['liste']][$anmeldung['team_id']]['tblock'] = Tabelle::get_team_block($anmeldung['team_id']);
            $liste[$anmeldung['liste']][$anmeldung['team_id']]['wertigkeit'] = Tabelle::get_team_wertigkeit($anmeldung['team_id']);
        }
        return db::escape($liste);
    }

    /********************
     * Turnierdatenergebnisse
     *******************/

    /**
     * Get alle Turnieranmeldungen des Turniers für den Spielplan nach Wertigkeit sortiert.
     *
     * @return array
     */
    function get_liste_spielplan(): array
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                SELECT turniere_liste.team_id, teams_liga.teamname, teams_liga.ligateam, teams_details.ligavertreter
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_details
                ON turniere_liste.team_id = teams_details.team_id
                WHERE turniere_liste.turnier_id='$turnier_id' AND turniere_liste.liste='spiele'
                ";
        $result = db::read($sql);

        $liste = [];
        // Welcher Spieltag ist relevant?
        while ($anmeldung = mysqli_fetch_assoc($result)) {
            $liste[$anmeldung['team_id']] = $anmeldung;
            $liste[$anmeldung['team_id']]['tblock'] = Tabelle::get_team_block($anmeldung['team_id'], $this->details['spieltag'] - 1);
            $liste[$anmeldung['team_id']]['wertigkeit'] = Tabelle::get_team_wertigkeit($anmeldung['team_id'], $this->details['spieltag'] - 1);
        }
        if (!empty($liste)) {
            // Array nach Wertigkeit sortieren
            uasort($liste, function ($team_a, $team_b) {
                return ((int) $team_b['wertigkeit'] <=> (int) $team_a['wertigkeit']);
            });
        }
        return db::escape($liste);
    }

    /**
     * Kaderliste für die Kaderkontrolle des Turniers;
     * @return array
     */
    function get_kader_kontrolle(): array
    {
        $teams = $this->get_liste_spielplan();
        foreach ($teams as $team) {
            $return[$team['team_id']] = Spieler::get_teamkader($team['team_id']);
        }
        return $return ?? [];
    }

    /**
     * Löscht Turnierergebnisse des Turnieres aus der DB
     */
    function delete_ergebnis()
    {
        $sql = "
        SELECT * 
        FROM turniere_ergebnisse
        WHERE turnier_id = $this->turnier_id
        ";
        if ((db::read($sql))->num_rows == 0) return;
        $sql = "
                DELETE FROM turniere_ergebnisse 
                WHERE turnier_id = $this->turnier_id
                ";
        db::write($sql);
        $this->log("Turnierergebnis wurde gelöscht.");
    }

    /**
     * Get Turnierergebnis des Turnieres
     * @return array
     */
    function get_ergebnis(): array
    {
        $sql = "
                SELECT * 
                FROM turniere_ergebnisse 
                WHERE turnier_id = $this->turnier_id
                ORDER BY platz
                ";
        $result = db::read($sql);
        while ($eintrag = mysqli_fetch_assoc($result)) {
            $return[$eintrag['platz']] = $eintrag;
        }
        return db::escape($return ?? []);
    }

    /**
     * @param $team_id
     * @param $ergebnis
     * @param $platz
     */
    function set_ergebnis($team_id, $ergebnis, $platz)
    {
        if (!in_array($this->details['art'], ['I', 'II', 'III'])) $ergebnis = 'NULL';
        $sql = "
                INSERT INTO turniere_ergebnisse (turnier_id, team_id, ergebnis, platz) 
                VALUES ($this->turnier_id, $team_id, $ergebnis, $platz);
                ";
        db::write($sql);
        $this->log("Ergebnis eingetragen: $platz. " . Team::teamid_to_teamname($team_id) . "($ergebnis Punkte)");
    }

    /**
     * Überträgt das Turnierergebnis der Platzierungstabelle in die Datenbank
     * @param Spielplan $spielplan
     */
    public static function set_ergebnisse(Spielplan $spielplan)
    {
        // Ergebnis eintragen
        $spielplan->turnier->set_phase('ergebnis');
        $spielplan->turnier->delete_ergebnis();
        foreach ($spielplan->platzierungstabelle as $team_id => $ergebnis) {
            $spielplan->turnier->set_ergebnis($team_id, $ergebnis['ligapunkte'], $ergebnis['platz']);
        }
        $spielplan->turnier->log("Turnierergebnis wurde in die Datenbank eingetragen");
        Form::affirm("Das Turnierergebnis wurde dem Ligaausschuss übermittelt und wird jetzt in den Ligatabellen angezeigt.");
    }

    /********************
     * Team an- und abmeldung
     *******************/

    /**
     * Ein Team zum Turnier anmelden
     *
     * Bei Anmeldung auf die Warteliste sollte $pos als die jeweilige Wartelistenposition übergeben werden
     * Könnnte man das auch mit nl_anmelden für nichtligateams zusammenlegen?
     *
     * @param $team_id
     * @param $liste
     * @param int $pos
     */

    function team_anmelden($team_id, $liste, $pos = 0)
    {
        $turnier_id = $this->turnier_id;
        //Handhabung der Warteliste
        if ($liste == 'warte') {
            $sql = "SELECT team_id, position_warteliste FROM turniere_liste WHERE turnier_id = $turnier_id AND position_warteliste >= $pos";
            $result = db::read($sql);
            while ($team = mysqli_fetch_assoc($result)) {
                $this->log("Warteliste: \r\n" . Team::teamid_to_teamname($team['team_id']) . " " . $team['position_warteliste'] . " -> " . ($team['position_warteliste'] + 1), "automatisch");
            }
            //Update der Wartelistepositionen
            $sql = "UPDATE turniere_liste SET position_warteliste = position_warteliste + 1 WHERE turnier_id = '$turnier_id' AND liste = 'warte' AND position_warteliste >= '$pos'";
            db::write($sql);
        }
        $sql = "INSERT INTO turniere_liste (turnier_id, team_id, liste, position_warteliste) VALUES ('$turnier_id', '$team_id','$liste', '$pos')";
        db::write($sql);
    }

    /**
     * Meldet ein Nichtligateam an
     *
     * Existiert bereits ein Nichtligateam mit gleichem Namen in der Datenbank, so wird dieses angemeldet es wird also
     * kein neues Nichtligateam erstellt
     *
     * Nichtligateams bekommen automatisch einen Stern hinter ihrem Namen
     *
     * @param $teamname
     * @param $liste
     * @param int $pos
     */
    function nl_anmelden($teamname, $liste, $pos = 0)
    {
        $turnier_id = $this->turnier_id;
        $teamname .= "*"; // Nichtligateams haben einen Stern hinter dem Namen
        if (empty(Team::teamname_to_teamid($teamname))) {
            $nl_team_id = db::get_auto_increment('teams_liga');
            $sql = "INSERT INTO teams_liga (teamname, ligateam) VALUES ('$teamname','Nein')";
            db::write($sql);
        } else {
            $nl_team_id = Team::teamname_to_teamid($teamname);
        }
        if ($liste == 'warte') {
            // Schreiben der Logs für die Warteliste //Siehe auch team_anmelden()
            $sql = "SELECT team_id, position_warteliste FROM turniere_liste WHERE turnier_id = $turnier_id AND position_warteliste >= $pos";
            $result = db::read($sql);
            while ($team = mysqli_fetch_assoc($result)) {
                $this->log("Warteliste: \r\n" . Team::teamid_to_teamname($team['team_id']) . " " . $team['position_warteliste'] . " -> " . ($team['position_warteliste'] + 1), "automatisch");
            }
            $sql = "UPDATE turniere_liste SET position_warteliste = position_warteliste + 1 WHERE turnier_id = '$turnier_id' AND liste = 'warte' AND position_warteliste >= '$pos'";
            db::write($sql);
        }
        $sql = "INSERT INTO turniere_liste (turnier_id, team_id, liste, position_warteliste) VALUES ('$turnier_id', '$nl_team_id','$liste', '$pos')";
        db::write($sql);
    }

    /**
     * Team via Freilos anmelden
     * @param $team_id
     */
    function freilos($team_id)
    {
        $turnier_id = $this->turnier_id;
        $team = new Team($team_id);
        $freilose = $team->get_freilose();
        $sql = "INSERT INTO turniere_liste (turnier_id, team_id, liste, freilos_gesetzt) VALUES ('$turnier_id','$team_id','spiele','Ja')";
        db::write($sql);
        $team->set_freilose($freilose - 1);
    }

    /**
     * Team wird von einem Turnier abgemeldet
     * @param int $team_id
     */
    function abmelden(int $team_id)
    {
        $turnier_id = $this->turnier_id;
        $sql = "DELETE FROM turniere_liste WHERE turnier_id = '$turnier_id' AND team_id = '$team_id'";
        db::write($sql);
    }

    /**
     * Sucht alle Wartelisteneinträge und sortiert diese der größe ihrer Position auf der Warteliste. Anschließend
     * werden die Wartelistenpostionen von 1 auf wieder vergeben
     *
     * Bsp: Position auf der Warteliste: 2 4 5 wird zu 1 2 3
     *
     * @param string $autor
     */
    function warteliste_aktualisieren(string $autor = 'automatisch')
    {
        //Für den Turnierlog
        $listen_vorher = $this->get_anmeldungen();
        $turnier_id = $this->turnier_id;
        //Warteliste korrigieren, wenn sich das Team von der Warteliste abmeldet
        $sql = "SELECT * FROM turniere_liste WHERE turnier_id = '$turnier_id' AND liste = 'warte' ORDER BY position_warteliste";
        $result = db::read($sql);
        $pos = 0;
        while ($team = mysqli_fetch_assoc($result)) {
            $pos += 1;
            $team_id = $team['team_id'];
            $sql = "
                    UPDATE turniere_liste 
                    SET position_warteliste = '$pos' 
                    WHERE turnier_id = '$turnier_id'
                    AND liste = 'warte'
                    AND team_id = '$team_id';
                    ";
            db::write($sql);
        }
        //Turnierlog schreiben
        $listen_nachher = $this->get_anmeldungen();
        foreach (($listen_vorher['warte'] ?? []) as $key => $team_vorher) {
            $team_nachher = $listen_nachher['warte'][$key];
            //Die Reihenfolge sollte sich nicht ändern dürfen, da get_anmeldungen nach der position auf der Warteliste sortiert
            if ($team_vorher['position_warteliste'] != $team_nachher['position_warteliste']) {
                $this->log("Warteliste aktualisieren: \r\n" . $team_vorher['teamname'] . " " . $team_vorher['position_warteliste'] . " -> " . $team_nachher['position_warteliste'], $autor);
            }
        }
    }

    /**
     * Füllt freie Plätze auf der Spielen-Liste von der Warteliste aus wieder auf,
     * wenn der Teamblock des Wartelisteneintrags zum Turnier passt,
     * wenn das Turnier nicht in der offenen Phase ist,
     * wenn das Turnier noch freie Plätze hat.
     *
     * @param string $autor
     * @param bool $send_mail
     */
    function spieleliste_auffuellen($autor = 'automatisch', $send_mail = true)
    {
        $daten = $this->details;
        $freie_plaetze = $this->get_anzahl_freie_plaetze();
        if ($daten['phase'] != 'offen' && $freie_plaetze > 0) {
            $liste = $this->get_anmeldungen(); // Order by Warteliste weshalb die Teams in der foreach schleife in der Richtigen reihenfolge behandelt werden
            foreach ($liste['warte'] as $team) {
                if ($this->check_team_block($team['team_id']) && $freie_plaetze > 0) { //Das Team wird abgemeldet, wenn es schon am Turnierdatum auf einer Spielen-Liste steht
                    if (!$this->check_doppel_anmeldung($team['team_id'])) {
                        $this->set_liste($team['team_id'], 'spiele'); //von Warteliste abmelden
                        $this->log("Spielen-Liste auffüllen: \r\n" . $team['teamname'] . " warte -> spiele", $autor);
                        if ($send_mail) {
                            MailBot::mail_warte_zu_spiele($this, $team['team_id']);
                        }
                        $freie_plaetze -= 1;
                    } else {
                        $this->abmelden($team['team_id']);
                        $this->log("Abgemeldet: \r\n" . $team['teamname'] . "Doppelanmeldung", $autor);
                    }
                }
            }
            $this->warteliste_aktualisieren();
        }
    }

    /**
     * Get Anzahl der freien Plätze auf dem Turnier
     * @return string
     */
    function get_anzahl_freie_plaetze(): string
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                SELECT 
                (SELECT plaetze FROM turniere_details WHERE turnier_id='$turnier_id')
                 - 
                (SELECT COUNT(liste_id) FROM turniere_liste WHERE turnier_id='$turnier_id' AND liste='spiele')
                AS freie_plaetze";
        $result = db::read($sql);
        $return = mysqli_fetch_assoc($result);
        return db::escape($return['freie_plaetze']);
    }

    /**
     * Ändert die Liste auf der sich ein Team befindet (Warte-, Melde- oder Spielen-Liste)
     *
     * @param int $team_id
     * @param string $liste
     * @param int $pos
     */
    function set_liste(int $team_id, string $liste, $pos = 0)
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                UPDATE turniere_liste 
                SET liste='$liste', position_warteliste='$pos' 
                WHERE turnier_id='$turnier_id' 
                AND team_id = '$team_id'
                ";
        db::write($sql);
    }

    /**
     * Gibt true aus, wenn das Team bereits zum Turnier angemeldet ist, sonst false
     *
     * @param int $team_id
     * @return bool
     */
    function check_team_angemeldet(int $team_id): bool
    {
        $turnier_id = $this->turnier_id;
        $sql = "SELECT liste FROM turniere_liste WHERE team_id='$team_id' AND turnier_id='$turnier_id'";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        if (!empty($result['liste'])) {
            return true;
        }
        return false;
    }

    /**
     * Gibt true aus, wenn das Team am Kalendertag des Turnieres bereits bei einem Turnier auf der Spielen-Liste steht
     *
     * @param int $team_id
     * @return bool
     */
    function check_doppel_anmeldung(int $team_id): bool
    {
        $datum = $this->details['datum'];
        $sql = "
                SELECT liste_id
                FROM turniere_liste
                INNER JOIN turniere_liga
                ON turniere_liste.turnier_id = turniere_liga.turnier_id
                WHERE team_id = '$team_id' 
                AND datum = '$datum' 
                AND liste = 'spiele'
                AND (turniere_liga.art = 'I' OR turniere_liga.art = 'II' OR turniere_liga.art = 'III')
                ";
        $result = db::read($sql);
        if (mysqli_num_rows($result) > 0) {
            return true;
        }
        return false;
    }

    /**
     * Get Liste eines angemeldeten Teams auf einem Turnier
     *
     * @param int $team_id
     * @return string
     */
    function get_liste(int $team_id): string
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                SELECT liste 
                FROM turniere_liste 
                WHERE team_id = '$team_id' 
                AND turnier_id ='$turnier_id'
                ";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        return db::escape($result['liste'] ?? '');
    }

    /**
     * Gibt true aus, wenn der Teamblock in das Turnier passt.
     *
     * @param $team_id
     * @return bool
     */
    function check_team_block($team_id): bool
    {
        if (!in_array($this->details['art'], ['I', 'II', 'III'])) {
            return false;
        }
        $team_block = Tabelle::get_team_block($team_id);
        $turnier_block = $this->details['tblock'];
        return self::check_team_block_static($team_block, $turnier_block);
    }

    /**
     * Statischer Check  des Teamblocks/Turnierblocks
     *
     * @param $team_block
     * @param $turnier_block
     * @return bool
     */
    public static function check_team_block_static($team_block, $turnier_block): bool
    {
        if ($team_block == 'NL') { //NL Teams können auch zu final, spass, fixed Turnieren angemeldet werden
            return true;
        } else {
            //Check ob es sich um ein Block-Turnier handelt (nicht spass oder finale)
            if (in_array($turnier_block, Config::BLOCK_ALL)) {
                //Block-String in Array auflösen
                $turnier_block = str_split($turnier_block);
                $team_block = str_split($team_block);
                //Check ob ein Buchstabe des Team-Blocks im Turnier-Block vorkommt
                foreach ($team_block as $buchstabe) {
                    if (in_array($buchstabe, $turnier_block)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /**
     * Gibt true aus, wenn das Team für das Turnier ein Ffreilos setzten darf
     * @param int $team_id
     * @return bool
     */
    function check_team_block_freilos(int $team_id): bool
    {
        $team_block = Tabelle::get_team_block($team_id);
        $turnier_block = $this->details['tblock'];
        return self::check_team_block_freilos_static($team_block, $turnier_block);
    }

    /**
     * Statischer Check Teamblock/Turnierblock bei Freilosen
     *
     * @param string $team_block
     * @param string $turnier_block
     * @return bool
     */
    public static function check_team_block_freilos_static(string $team_block, string $turnier_block): bool
    {
        //Check ob es sich um einen Block-Turnier handelt (nicht spass, finale, oder fix)
        if (in_array($turnier_block, Config::BLOCK_ALL)) {
            $pos_turnier = array_search($turnier_block, Config::BLOCK_ALL, true);
            $team_block = str_split($team_block);
            for ($i = $pos_turnier; $i <= (count(Config::BLOCK_ALL) - 1); $i++) {
                foreach ($team_block as $buchstabe) {
                    $turnier_block = str_split(Config::BLOCK_ALL[$i]);
                    if (in_array($buchstabe, $turnier_block)) {
                        return true;
                    }
                }
            }
        }
        return false;
    }

    /********************
     * Turnierdaten ändern
     *******************/

    /**
     * Hinterlegt zu einem Turnier einen Link zu einem manuelle hochgeladenen Spielplan
     *
     * Pfad zum Spielplan
     * @param string $link
     * @param string $phase
     */
    function upload_spielplan(string $link, string $phase)
    {
        $sql = "
                UPDATE turniere_details
                SET link_spielplan = '$link'
                WHERE turnier_id = $this->turnier_id;
                ";
        db::write($sql);
        $this->details['link_spielplan'] = $link;
        $this->log("Manuelle Spielplan- oder Ergebnisdatei wurde hochgeladen.");
        $this->set_phase("$phase");
    }

    /**
     * Gibt den Link zum Liga-Spielplan aus, je nach dem ob er manuell hochgeladen oder automatisch erstellt wurde
     * @return string
     */
    function get_spielplan_link(): string
    {
        return  (empty($this->details['link_spielplan']))
                ? Config::BASE_LINK . '/liga/spielplan.php?turnier_id=' . $this->turnier_id
                : $this->details['link_spielplan'];
    }

    /**
     * Gibt den Link zum Teamcenter-Spielplan aus, je nach dem ob er manuell hochgeladen oder automatisch erstellt wurde
     * @return string
     */
    function get_spielplan_link_tc(): string
    {
        return (empty($this->details['link_spielplan']))
            ? '../teamcenter/tc_spielplan.php?turnier_id=' . $this->turnier_id
            : $this->details['link_spielplan'];
    }

    /**
     * Gibt den Link zum Ligacenter-Spielplan aus, je nach dem ob er manuell hochgeladen oder automatisch erstellt wurde
     * @return string
     */
    function get_spielplan_link_lc(): string
    {
        return (empty($this->details['link_spielplan']))
            ? '../teamcenter/lc_spielplan.php?turnier_id=' . $this->turnier_id
            : $this->details['link_spielplan'];
    }

    /**
     * Ändert die Phase in der sich das Turnier befindet
     *
     * @param string $phase
     */
    function set_phase(string $phase)
    {
        if ($phase === $this->details['phase']) return;

        $sql = "
                UPDATE turniere_liga 
                SET phase = '$phase' 
                WHERE turnier_id = $this->turnier_id
                ";
        db::write($sql);
        $this->log("Phase:" . $this->details['phase'] . " => " . $phase);
        $this->details['phase'] = $phase;
    }

    /**
     * Ändert den Turnierblock
     *
     * @param string $block
     */
    function set_turnier_block(string $block)
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                UPDATE turniere_liga 
                SET tblock='$block' 
                WHERE turnier_id='$turnier_id'
                ";
        db::write($sql);
        $this->details['tblock'] = $block;
    }

    /**
     * Setzt den Spieltag in der Datenbank fest
     *
     * @param $spieltag
     */
    function set_spieltag($spieltag)
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                UPDATE turniere_liga
                SET spieltag='$spieltag'
                WHERE turnier_id='$turnier_id'
                ";
        db::write($sql);
        $this->details['spieltag'] = $spieltag;
    }

    /**
     * Update der Turnierdetails
     *
     * @param string $startzeit
     * @param string $besprechung
     * @param string $plaetze
     * @param string $spielplan
     * @param string $hallenname
     * @param string $strasse
     * @param string $plz
     * @param string $ort
     * @param string $haltestellen
     * @param string $hinweis
     * @param string $startgebuehr
     * @param string $name
     * @param string $handy
     * @return bool
     */
    function change_turnier_details(string $startzeit, string $besprechung, string $plaetze, string $spielplan,
                                    string $hallenname, string $strasse, string $plz, string $ort, string $haltestellen,
                                    string $hinweis, string $startgebuehr, string $name, string $handy): bool
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                UPDATE turniere_details 
                SET hallenname = '$hallenname', strasse = '$strasse', plz = '$plz', ort = '$ort',
                haltestellen = '$haltestellen', plaetze = '$plaetze', spielplan = '$spielplan',
                startzeit = '$startzeit', besprechung = '$besprechung', hinweis = '$hinweis', organisator = '$name',
                handy = '$handy', startgebuehr = '$startgebuehr'
                WHERE turnier_id  =  '$turnier_id'
                ";
        db::write($sql);
        return true;
    }

    /**
     * Update der Turnier-Ligadaten
     *
     * @param string $tname
     * @param string $ausrichter
     * @param string $art
     * @param string $tblock
     * @param string $fixed
     * @param string $datum
     * @param string $phase
     * @return bool
     */
    function change_turnier_liga(string $tname, string $ausrichter, string $art, string $tblock, string $fixed,
                                 string $datum, string $phase): bool
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                UPDATE turniere_liga 
                SET tname='$tname', phase='$phase', ausrichter='$ausrichter', art='$art', tblock='$tblock', 
                tblock_fixed='$fixed', datum='$datum'
                WHERE turnier_id = '$turnier_id'
                ";
        db::write($sql);
        return true;
    }

    /**
     * Ändert den Turnierblock
     *
     * @param $tblock
     * @param $fixed
     * @param $art
     * @return bool
     */
    function change_turnier_block($tblock, $fixed, $art): bool
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                UPDATE turniere_liga 
                SET tblock = '$tblock', tblock_fixed = '$fixed', art = '$art'
                WHERE turnier_id = '$turnier_id'
                ";
        db::write($sql);
        return true;
    }

    /**
     * Schreibt in den Turnierlog
     *
     * @param string $log_text
     * @param string $autor
     */
    function log(string $log_text, string $autor = '')
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                INSERT INTO turniere_log (turnier_id, log_text, autor) 
                VALUES ('$turnier_id','$log_text', '$autor');
                ";
        db::write($sql);
    }

    //

    /**
     * Get Turnierlogs
     *
     * @return array
     */
    function get_logs(): array
    {
        $turnier_id = $this->turnier_id;
        $sql = "
                SELECT * 
                FROM turniere_log 
                WHERE turnier_id = '$turnier_id'
                ";
        $result = db::read($sql);
        $logs = [];
        while ($x = mysqli_fetch_assoc($result)) {
            array_push($logs, $x);
        }
        return db::escape($logs);
    }

    /**
     * Löscht das Turnier aus der DB und vermerkt das Turnier in der Tabelle gelöschte TUrniere
     *
     * Grund des Löschens
     * @param string $grund
     */
    function delete($grund = '')
    {
        db::db_sichern();
        $ort = $this->details['ort'];
        $datum = $this->details['datum'];
        $saison = $this->details['saison'];

        // Turnier in der Datenbank vermerken
        $sql = "
                INSERT INTO turniere_geloescht (turnier_id, datum, ort, grund, saison) 
                VALUES ('$this->turnier_id','$datum','$ort','$grund','$saison')
                ";
        db::write($sql);

        // Turnier aus der Datenbank löschen
        $sql = "
                DELETE FROM turniere_liga 
                WHERE turnier_id = $this->turnier_id
                ";
        db::write($sql);

        // Spieltage neu sortieren
        Ligabot::set_spieltage();
    }

    /**
     * Liste an gelöschten Turnieren
     *
     * @return array
     */
    public static function get_deleted_turniere(): array
    {
        $sql = "
                SELECT * 
                FROM turniere_geloescht 
                WHERE saison = '" . Config::SAISON . "' 
                ORDER BY datum DESC
                ";
        $result = db::read($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            $turniere_deleted[$x['turnier_id']] = $x;
        }
        return db::escape($turniere_deleted ?? []);
    }
}