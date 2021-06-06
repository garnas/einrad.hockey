<?php

/**
 * Class Turnier
 *
 * Alles für die Verwaltung und zum Anzeigen von Turnieren
 */
class Turnier
{

    /**
     * Eindeutige Turnier-ID
     * @var int
     */
    public int $id;
    /**
     * Unixdatum
     * @var int
     */
    public int $unix;
    /**
     * Array aller Turnierdaten
     * @var array
     */
    public array $details;
    /**
     * Logtext
     */
    private string $log = '';

    /**
     * Turnier constructor.
     * @param $turnier_id
     */
    public function __construct($turnier_id)
    {
        $this->id = $turnier_id;
        $this->details = $this->get_details();
        $this->unix = strtotime($this->details['datum']);
    }

    /**
     * Schreibt den Turnierlog bei Zerstörung des Objektes
     */
    public function __destruct()
    {
        if (!empty($this->log)) {
            $sql = "
                INSERT INTO turniere_log (turnier_id, log_text, autor) 
                VALUES ($this->id, ?, ?);
                ";
            $autor = Helper::get_akteur(true);
            db::$db->query($sql, trim($this->log), $autor)->log();
        }
    }

    /**
     * Erstellt eine Turniervorlage zum Ausfüllen in der Datenbank
     *
     * @param int $ausrichter
     * @return Turnier
     */
    public static function new_turnier(int $ausrichter): Turnier
    {
        $sql = "
                INSERT INTO turniere_liga (ausrichter, phase) 
                VALUES (?, 'offen')
                ";
        db::$db->query($sql, $ausrichter)->log();

        $turnier_id = db::$db->get_last_insert_id();

        $sql = "
                INSERT INTO turniere_details (turnier_id) 
                VALUES (?)
                ";
        db::$db->query($sql, $turnier_id)->log();

        $turnier = new Turnier ($turnier_id);
        $turnier->log("Turnier wurde erstellt. (Ausrichter $ausrichter)");
        $turnier->anmelden($ausrichter, 'spiele');
        return $turnier;
    }

    /**
     * Schreibt einen Eintrag in die turniere_liga Tabelle
     *
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    public function set_liga(string $column, mixed $value): Turnier
    {
        if ($this->details[$column] === $value) {
            return $this;
        }
        $column_esc = db::escape_column('turniere_liga', $column);
        $sql = "
                UPDATE turniere_liga
                SET $column_esc = ?
                WHERE turnier_id = $this->id
                ";

        db::$db->query($sql, $value)->log();
        $this->details[$column] = $value;
        $this->log(ucfirst($column) . ": $value");
        return $this;
    }

    /**
     * Schreibt einen Eintrag in die turniere_details Tabelle
     *
     * @param string $column
     * @param mixed $value
     * @return $this
     */
    public function set(string $column, mixed $value): Turnier
    {
        if ($this->details[$column] === $value) {
            return $this;
        }
        $column_esc = db::escape_column('turniere_details', $column);
        $sql = "
                UPDATE turniere_details
                SET $column_esc = ?
                WHERE turnier_id = $this->id
                ";

        db::$db->query($sql, $value)->log();
        $this->details[$column] = $value;
        $this->log(ucfirst($column) . ": $value");

        return $this;
    }

    /**
     * Alle Turnierdaten auf einmal
     *
     * Where-Klause für SQl-Query
     * @param string $phase
     * @param bool $equal
     * @param bool $asc
     * @param int $saison
     * @return array
     */
    public static function get_turniere(string $phase,
                                        bool $equal = true,
                                        bool $asc = true,
                                        int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                WHERE phase " . ($equal ? "=" : "!=") . " ?
                AND saison = ?
                ORDER BY turniere_liga.datum " . ($asc ? "asc" : "desc");
        return db::$db->query($sql, $phase, $saison)->esc()->fetch('turnier_id');
    }

    /**
     * Get Turniere, welche ein Team ausrichtet.
     *
     * @param int $team_id Ausrichter
     * @return array
     */
    public static function get_eigene_turniere(int $team_id): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname 
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON teams_liga.team_id = turniere_liga.ausrichter
                AND ausrichter = ?
                AND saison >= " . Config::SAISON - 1 . " 
                ORDER BY turniere_liga.datum desc
                ";
        return db::$db->query($sql, $team_id)->esc()->fetch('turnier_id');
    }

    /**
     * Turnierdetails von nur einem Turnier erhalten
     *
     * @return array
     */
    public function get_details(): array
    {
        $sql = "
                SELECT turniere_liga.*, turniere_details.*, teams_liga.teamname
                FROM turniere_liga 
                INNER JOIN turniere_details 
                ON turniere_liga.turnier_id = turniere_details.turnier_id
                INNER JOIN teams_liga
                ON turniere_liga.ausrichter = teams_liga.team_id
                WHERE turniere_liga.turnier_id = $this->id
                ";
        return db::$db->query($sql)->esc()->fetch_row();

    }

    /**
     * Alle Turnieranmeldelisten auf einmal
     *
     * @param int $saison
     * @return array [turnier_id][liste][team_id]
     */
    public static function get_all_anmeldungen(int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT turniere_liste.*, teams_liga.teamname, teams_liga.ligateam
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                INNER JOIN turniere_liga
                ON turniere_liga.turnier_id = turniere_liste.turnier_id
                WHERE turniere_liga.saison = ?
                AND turniere_liga.phase != 'ergebnis'
                ORDER BY turniere_liste.position_warteliste
                ";
        $anmeldungen = db::$db->query($sql, $saison)->esc()->fetch();
        $spieltag = Tabelle::get_aktuellen_spieltag();
        foreach ($anmeldungen as $a) {
            $turnier_listen[$a['turnier_id']][$a['liste']][$a['team_id']] = $a;
            $turnier_listen[$a['turnier_id']][$a['liste']][$a['team_id']]['tblock'] = Tabelle::get_team_block($a['team_id'], $spieltag);
            $turnier_listen[$a['turnier_id']][$a['liste']][$a['team_id']]['wertigkeit'] = Tabelle::get_team_wertigkeit($a['team_id'], $spieltag);
        }
        return $turnier_listen ?? [];
    }

    /**
     * Anmeldungen der Warte-, Melde-, Spielen-Liste eines Turnieres
     * Format: zb. $liste['liste'][Array der Anmeldedaten der Angemeldeten Teams auf der entsprechenden Liste]
     * @return array
     */
    public function get_anmeldungen(): array
    {
        $sql = "
                SELECT turniere_liste.*, teams_liga.teamname, teams_liga.ligateam
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                WHERE turniere_liste.turnier_id = $this->id
                ORDER BY turniere_liste.position_warteliste
                ";
        $anmeldungen = db::$db->query($sql)->esc()->fetch();
        $liste['team_ids'] = $liste['teamnamen'] = $liste['spiele'] = $liste['melde'] = $liste['warte'] = [];
        foreach ($anmeldungen as $a) {
            $liste[$a['liste']][$a['team_id']] = $a;
            $liste[$a['liste']][$a['team_id']]['tblock'] = Tabelle::get_team_block($a['team_id']);
            $liste[$a['liste']][$a['team_id']]['wertigkeit'] = Tabelle::get_team_wertigkeit($a['team_id']);
        }
        return $liste;
    }

    /**
     * Get alle Turnieranmeldungen des Turniers für den Spielplan nach Wertung sortiert.
     *
     * @return array
     */
    public function get_liste_spielplan(): array
    {
        $sql = "
                SELECT turniere_liste.team_id, teams_liga.teamname, teams_liga.ligateam,
                    teams_details.ligavertreter, teams_details.trikot_farbe_1, teams_details.trikot_farbe_2
                FROM turniere_liste
                LEFT JOIN teams_liga
                ON turniere_liste.team_id = teams_liga.team_id
                LEFT JOIN teams_details
                ON turniere_liste.team_id = teams_details.team_id
                WHERE turniere_liste.turnier_id = $this->id 
                AND turniere_liste.liste = 'spiele'
                ";
        $spielen_liste = db::$db->query($sql)->esc()->fetch('team_id');
        // Blöcke und Wertungen hinzufügen
        foreach ($spielen_liste as $team_id => $anmeldung) {
            $spielen_liste[$team_id]['tblock']
                = Tabelle::get_team_block($anmeldung['team_id'], $this->details['spieltag'] - 1);
            $spielen_liste[$team_id]['wertigkeit']
                = Tabelle::get_team_wertigkeit($anmeldung['team_id'], $this->details['spieltag'] - 1);
        }
        if (!empty($spielen_liste)) {
            // Array nach Wertung sortieren
            uasort($spielen_liste, static function ($team_a, $team_b) {
                return ((int)$team_b['wertigkeit'] <=> (int)$team_a['wertigkeit']);
            });
        }
        return $spielen_liste ?? [];
    }

    /**
     * Kaderliste für die Kaderkontrolle des Turniers
     *
     * @return array
     */
    public function get_kader_kontrolle(): array
    {
        $spielen_liste = $this->get_liste_spielplan();
        foreach ($spielen_liste as $team) {
            $return[$team['team_id']] = Spieler::get_teamkader($team['team_id']);
        }
        return $return ?? [];
    }

    /**
     * Löscht Turnierergebnisse des Turnieres aus der DB
     */
    public function delete_ergebnis(): void
    {
        $sql = "
                DELETE FROM turniere_ergebnisse 
                WHERE turnier_id = $this->id
                ";
        db::$db->query($sql)->log();
        if (db::$db->affected_rows() > 0) $this->log("Turnierergebnisse wurden gelöscht.");
    }

    /**
     * Get Turnierergebnis des Turnieres
     * @return array
     */
    public function get_ergebnis(): array
    {
        $sql = "
                SELECT * 
                FROM turniere_ergebnisse 
                WHERE turnier_id = $this->id
                ORDER BY platz
                ";

        return db::$db->query($sql)->esc()->fetch('platz');
    }

    /**
     * Schreibt ein Ergebnis eines Teams in die Datenbank
     *
     * @param int $team_id
     * @param int|null $ergebnis
     * @param int $platz
     */
    public function set_ergebnis(int $team_id, int|null $ergebnis, int $platz): void
    {
        if (!in_array($this->details['art'], ['I', 'II', 'III'])) {
            $ergebnis = NULL;
        }
        $sql = "
                INSERT INTO turniere_ergebnisse (turnier_id, team_id, ergebnis, platz) 
                VALUES ($this->id, ?, ?, ?);
                ";
        db::$db->query($sql, $team_id, $ergebnis, $platz)->log();
    }

    /**
     * Überträgt das Turnierergebnis der Platzierungstabelle in die Datenbank
     *
     * @param array $platzierungstabelle
     */
    public function set_ergebnisse(array $platzierungstabelle): void
    {
        // Ergebns eintragen
        if (!empty($this->get_ergebnis())) {
            $this->delete_ergebnis();
        }
        foreach ($platzierungstabelle as $team_id => $ergebnis) {
            $this->set_ergebnis($team_id, $ergebnis['ligapunkte'], $ergebnis['platz']);
        }
        $this->set_liga('phase', 'ergebnis');
        $this->log("Turnierergebnis wurde in die Datenbank eingetragen");
    }

    /**
     * Ein Team zum Turnier anmelden
     *
     * Bei Anmeldung auf die Warteliste sollte $pos als die jeweilige Wartelistenposition übergeben werden
     * Könnnte man das auch mit nl_anmelden für nichtligateams zusammenlegen?
     *
     * @param int $team_id
     * @param string $liste
     * @param int $pos
     */

    public function anmelden(int $team_id, string $liste, int $pos = 0): void
    {
        // Update der Wartelistepositionen
        if ($liste === 'warte') {
            $sql = "
                    UPDATE turniere_liste 
                    SET position_warteliste = position_warteliste + 1 
                    WHERE turnier_id = $this->id 
                    AND liste = 'warte' 
                    AND position_warteliste >= ?";
            db::$db->query($sql, $pos)->log();
            if (db::$db->affected_rows() > 0) {
                $this->log("Warteliste aktualisiert");
            }
        }
        $sql = "
                INSERT INTO turniere_liste (turnier_id, team_id, liste, position_warteliste) 
                VALUES ($this->id, ?, ?, ?)
                ";
        db::$db->query($sql, $team_id, $liste, $pos)->log();
        $this->log(
            "Anmeldung:\r\n" . Team::id_to_name($team_id) . " ($liste)"
            . (($liste === 'warte') ? "\r\nWartepos: $pos" : '')
            . "\r\nTeamb.: " . Tabelle::get_team_block($team_id) . " | Turnierb. " . $this->details['tblock']);
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
    public function nl_anmelden($teamname, $liste, $pos = 0): void
    {
        $teamname .= "*"; // Nichtligateams haben einen Stern hinter dem Namen
        if (Team::name_to_id($teamname) === NULL) { //TODO === NULL testen
            $sql = "
                    INSERT INTO teams_liga (teamname, ligateam) 
                    VALUES (?, 'Nein')
                    ";
            db::$db->query($sql, $teamname)->log();
            $nl_team_id = db::$db->get_last_insert_id();
        } else {
            $nl_team_id = Team::name_to_id($teamname);
        }
        $this->anmelden($nl_team_id, $liste, $pos);
    }

    /**
     * Team via Freilos anmelden
     *
     * @param int $team_id
     */
    public function freilos(int $team_id): void
    {
        // Freilos abziehen
        $team = new Team($team_id);
        $freilose = $team->get_freilose();
        $team->set_freilose($freilose - 1);

        // Auf die Spielenliste setzen
        $sql = "
                INSERT INTO turniere_liste (turnier_id, team_id, liste, freilos_gesetzt) 
                VALUES ($this->id, ?, 'spiele', 'Ja')
                ";
        db::$db->query($sql, $team_id)->log();

        $this->log(
            "Freilos:\r\n" . Team::id_to_name($team_id) . " (spiele)"
            . "\r\nTeamb.: " . Tabelle::get_team_block($team_id) . " | Turnierb. " . $this->details['tblock']);
    }

    /**
     * Team wird von einem Turnier abgemeldet
     * @param int $team_id
     */
    public function abmelden(int $team_id): void
    {
        $sql = "
                DELETE FROM turniere_liste 
                WHERE turnier_id = $this->id
                AND team_id = ?
                ";
        db::$db->query($sql, $team_id)->log();
        if (db::$db->affected_rows() > 0) $this->log("Abmeldung:\r\n" . Team::id_to_name($team_id));
    }

    /**
     * Sucht alle Wartelisteneinträge und sortiert diese der größe ihrer Position auf der Warteliste. Anschließend
     * werden die Wartelistenpostionen von 1 auf wieder vergeben
     *
     * Bsp: Position auf der Warteliste: 2 4 5 wird zu 1 2 3
     *
     */
    public function warteliste_aktualisieren(): void
    {
        // Warteliste holen
        $sql = "
                SELECT * 
                FROM turniere_liste 
                WHERE turnier_id = $this->id 
                AND liste = 'warte' 
                ORDER BY position_warteliste
                ";
        $liste = db::$db->query($sql)->fetch('team_id');

        // Warteliste korrigieren
        $pos = $affected_rows = 0;
        foreach ($liste as $team) {
            $sql = "
                    UPDATE turniere_liste 
                    SET position_warteliste = ?
                    WHERE turnier_id = $this->id
                    AND liste = 'warte'
                    AND team_id = ?;
                    ";
            db::$db->query($sql, ++$pos, $team['team_id'])->log();
            $affected_rows += db::$db->affected_rows();
            $logs[] = $pos . ". " . Team::id_to_name($team['team_id']);
        }
        if ($affected_rows > 0) {
            $this->log("Warteliste aktualisiert:\r\n" . implode("\r\n", $logs ?? []));
        }
    }

    /**
     * Füllt freie Plätze auf der Spielen-Liste von der Warteliste aus wieder auf,
     * wenn der Teamblock des Wartelisteneintrags zum Turnier passt,
     * wenn das Turnier nicht in der offenen Phase ist,
     * wenn das Turnier noch freie Plätze hat.
     *
     * @param bool $send_mail
     */
    public function spieleliste_auffuellen($send_mail = true): void //TODO Keine NLs nachrücken, wenn < 4 Ligateams, aber dann wieder NL mitrücken wenn ok
    {
        $freie_plaetze = $this->get_anzahl_freie_plaetze();
        $log = false;

        if ($this->details['phase'] === 'melde' && $freie_plaetze > 0) {
            $liste = $this->get_anmeldungen();// Order by Warteliste weshalb die Teams in der foreach schleife in der Richtigen reihenfolge behandelt werden

            foreach ($liste['warte'] as $team) {
                if ($this->check_team_block($team['team_id']) && $freie_plaetze > 0) {
                    if ($this->check_doppel_anmeldung($team['team_id'])) {
                        $this->abmelden($team['team_id']);
                    } else { // Das Team wird abgemeldet, wenn es schon am Turnierdatum auf einer Spielen-Liste steht
                        $this->set_liste($team['team_id'], 'spiele');
                        if ($send_mail) {
                            MailBot::mail_warte_zu_spiele($this, $team['team_id']);
                        }
                        --$freie_plaetze;
                        $log = true;
                    }
                }
            }

            if ($log) {
                $this->log("Spielen-Liste aufgefüllt");
            }

            $this->warteliste_aktualisieren();
        }
    }

    /**
     * Get Anzahl der freien Plätze auf dem Turnier
     * @return string
     */
    public function get_anzahl_freie_plaetze(): string
    {
        $sql = "
                SELECT 
                (SELECT plaetze FROM turniere_details WHERE turnier_id = $this->id)
                 - 
                (SELECT COUNT(liste_id) FROM turniere_liste WHERE turnier_id = $this->id AND liste = 'spiele')
                ";
        return db::$db->query($sql)->esc()->fetch_one();
    }

    /**
     * Ändert die Liste auf der sich ein Team befindet (Warte-, Melde- oder Spielen-Liste)
     *
     * @param int $team_id
     * @param string $liste
     * @param int $pos
     */
    public function set_liste(int $team_id, string $liste, $pos = 0): void
    {
        $sql = "
                UPDATE turniere_liste 
                SET liste = ?, position_warteliste = ? 
                WHERE turnier_id = $this->id 
                AND team_id = ?
                ";
        db::$db->query($sql, [$liste, $pos, $team_id]);
        $this->log("Listenwechsel:\r\n"
            . Team::id_to_name($team_id) . " ($liste)"
            . (($liste === 'warte') ? "\r\nWartepos: $pos" : ''));
    }

    /**
     * Gibt true aus, wenn das Team bereits zum Turnier angemeldet ist, sonst false
     *
     * @param int $team_id
     * @return bool
     */
    public function check_team_angemeldet(int $team_id): bool
    {
        $sql = "
                SELECT liste 
                FROM turniere_liste 
                WHERE team_id = ? AND turnier_id = $this->id
                ";
        return (db::$db->query($sql, $team_id)->num_rows() > 0);
    }

    /**
     * Gibt true aus, wenn das Team am Kalendertag des Turnieres bereits bei einem Turnier auf der Spielen-Liste steht
     *
     * @param int $team_id
     * @return bool
     */
    public function check_doppel_anmeldung(int $team_id): bool
    {
        $sql = "
                SELECT liste_id
                FROM turniere_liste
                INNER JOIN turniere_liga
                ON turniere_liste.turnier_id = turniere_liga.turnier_id
                WHERE team_id = ? 
                AND datum = ? 
                AND liste = 'spiele'
                AND (turniere_liga.art = 'I' OR turniere_liga.art = 'II' OR turniere_liga.art = 'III')
                ";
        return db::$db->query($sql, $team_id, $this->details['datum'])->num_rows() > 0;
    }

    /**
     * Get Liste eines angemeldeten Teams auf einem Turnier
     *
     * @param int $team_id
     * @return string
     */
    public function get_liste(int $team_id): string
    {
        $sql = "
                SELECT liste 
                FROM turniere_liste 
                WHERE team_id = ? 
                AND turnier_id = $this->id
                ";
        return db::$db->query($sql, $team_id)->esc()->fetch_one() ?? '';
    }

    /**
     * Gibt true aus, wenn der Teamblock in das Turnier passt.
     *
     * @param $team_id
     * @return bool
     */
    public function check_team_block(int $team_id): bool
    {
        // Falsche Turnierart für Blockcheck
        if (!in_array($this->details['art'], ['I', 'II', 'III'])) {
            return false;
        }

        return self::check_team_block_static(Tabelle::get_team_block($team_id), $this->details['tblock']);
    }

    /**
     * Statischer Check  des Teamblocks/Turnierblocks
     *
     * @param string $team_block
     * @param string $turnier_block
     * @return bool
     */
    public static function check_team_block_static(string $team_block, string $turnier_block): bool
    {
        if ($team_block === NULL) {
            return true;
        } // NL Teams können immer angemeldet werden

        // Check ob es sich um ein Block-Turnier handelt (nicht spass oder finale)
        if (in_array($turnier_block, Config::BLOCK_ALL)) {
            // Block-String in Array auflösen
            $turnier_buchstaben = str_split($turnier_block);
            $team_buchstaben = str_split($team_block);
            // Check ob ein Buchstabe des Team-Blocks im Turnier-Block vorkommt
            foreach ($team_buchstaben as $buchstabe) {
                if (in_array($buchstabe, $turnier_buchstaben)) {
                    return true;
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
    public function check_team_block_freilos(int $team_id): bool
    {
        return self::check_team_block_freilos_static(Tabelle::get_team_block($team_id), $this->details['tblock']);
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
        // Check ob es sich um einen Block-Turnier handelt (nicht spass, finale, oder fix)
        if (in_array($turnier_block, Config::BLOCK_ALL)) {
            $pos_turnier = array_search($turnier_block, Config::BLOCK_ALL, true);
            $team_block = str_split($team_block);
            for ($i = $pos_turnier; $i <= (count(Config::BLOCK_ALL) - 1); $i++) {
                foreach ($team_block as $buchstabe) {
                    $turnier_block = str_split(Config::BLOCK_ALL[$i]);
                    if (in_array($buchstabe, $turnier_block)) return true;
                }
            }
        }
        return false;
    }

    /**
     * Hinterlegt zu einem Turnier einen Link zu einem manuelle hochgeladenen Spielplan
     *
     * Pfad zum Spielplan
     * @param string $link
     * @param string $phase
     */
    public function upload_spielplan(string $link, string $phase): void
    {
        $sql = "
                UPDATE turniere_liga
                SET spielplan_datei = ?
                WHERE turnier_id = $this->id;
                ";
        db::$db->query($sql, $link)->log();
        $this->details['spielplan_datei'] = $link;
        $this->set_liga('phase', $phase);
        $this->log("Manuelle Spielplan- oder Ergebnisdatei wurde hochgeladen.");

    }

    /**
     * Gibt den Link zum Liga-Spielplan aus, je nach dem ob er manuell hochgeladen oder automatisch erstellt wurde
     *
     * @param string $scope default: allgemein, lc => ligacenter tc => teamcenter
     * @return false|string
     */
    public function get_spielplan_link(string $scope = ''): false|string
    {
        // Es existiert ein manuell hochgeladener Spielplan
        if (!empty($this->details['spielplan_datei'])) {
            return $this->details['spielplan_datei'];
        }

        // Es existiert ein automatisch erstellter Spielplan
        if (!empty($this->details['spielplan_vorlage'])) {
            return match ($scope) {
                'lc' => Env::BASE_URL . '/ligacenter/lc_spielplan.php?turnier_id=' . $this->id,
                'tc' => Env::BASE_URL . '/teamcenter/tc_spielplan.php?turnier_id=' . $this->id,
                default => Env::BASE_URL . '/liga/spielplan.php?turnier_id=' . $this->id
            };
        }

        return false;
    }

    /**
     * Schreibt in den Turnierlog.
     *
     * Turnierlogs werden bei Zerstörung des Objektes in die DB geschrieben.
     *
     * @param string $log_text
     */
    public function log(string $log_text): void
    {
        $this->log .= "\r\n" . $log_text;
    }

    /**
     * Get Turnierlogs
     *
     * @return array
     */
    public function get_logs(): array
    {
        $sql = "
                SELECT * 
                FROM turniere_log 
                WHERE turnier_id = $this->id
                ";
        return db::$db->query($sql)->esc()->fetch();
    }

    /**
     * Löscht das Turnier aus der DB und vermerkt das Turnier in der Tabelle gelöschte TUrniere
     *
     * Grund des Löschens
     * @param string $grund
     */
    public function delete($grund = ''): void
    {
        // Datenbank Backup
        db::sql_backup();

        // Turnier in der Datenbank vermerken
        $sql = "
                INSERT INTO turniere_geloescht (turnier_id, datum, ort, grund, saison) 
                VALUES ($this->id, ?, ?, ?, ?)
                ";
        $params = [$this->details['datum'], $this->details['ort'], $grund, $this->details['saison']];
        db::$db->query($sql, $params)->log();

        // Turnier aus der Datenbank löschen
        $sql = "
                DELETE FROM turniere_liga 
                WHERE turnier_id = $this->id
                ";
        db::$db->query($sql)->log();
        $this->log("Turnier wurde gelöscht.");
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
        return db::$db->query($sql)->esc()->fetch('turnier_id');
    }

    public function parse(array $string, $short = false): array
    {
        foreach ($this->details as $detail){
            $this->details['wochentag'] = strftime("%A", strtotime($this->details['datum']));

            $turniere[$turnier_id]['datum'] = strftime("%d.%m.", strtotime($turniere[$turnier_id]['datum']));
        }
//        return match ($string){
//            'spass' => 'Spaßturnier',
//            'final' => 'Finalturnier',
//            'spass' => 'Spaßturnier',
//            'spass' => 'Spaßturnier',
//            'spass' => 'Spaßturnier',
//            'spass' => 'Spaßturnier',
//        };

        //Turnierdarten parsen
        foreach ($turniere as $turnier_id => $turnier) {
            $turniere[$turnier_id]['wochentag'] =

            $turniere[$turnier_id]['startzeit'] = substr($turniere[$turnier_id]['startzeit'], 0, -3);

            if ($turniere[$turnier_id]['art'] == 'spass') {
                $turniere[$turnier_id]['tblock'] = 'Spaß';
            }
            if ($turniere[$turnier_id]['besprechung'] == 'Ja') {
                $turniere[$turnier_id]['besprechung'] = 'Gemeinsame Teambesprechung um ' . date('H:i', strtotime($turniere[$turnier_id]['startzeit']) - 15 * 60) . '&nbsp;Uhr';
            } else {
                $turniere[$turnier_id]['besprechung'] = '';
            }
            // Spielmodus
            if ($turniere[$turnier_id]['format'] == 'jgj') {
                $turniere[$turnier_id]['format'] = 'Jeder-gegen-Jeden';
            } elseif ($turniere[$turnier_id]['format'] == 'dko') {
                $turniere[$turnier_id]['format'] = 'Doppel-KO';
            } elseif ($turniere[$turnier_id]['format'] == 'gruppen') {
                $turniere[$turnier_id]['format'] = 'zwei Gruppen';
            }
        }
    }
}