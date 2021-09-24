<?php

/**
 * Class Team
 * Alle Funktionen zum Darstellen und Verwalten von Teamdaten
 */
class Team
{
    /**
     * TeamID zur eindeutigen Identifikation
     * @var int
     */
    public int $id;
    public array $details;

    /**
     * Werden nur bei Bedarf gesetzt.
     * Dazu müssen dann die entsprechenden Setter aufgerufen werden.
     */
    public ?int $wertigkeit;
    public ?string $tblock;
    public ?int $rang;
    public ?int $position_warteliste;

    /**
     * Team constructor.
     * @param $team_id
     */
    public function __construct($team_id)
    {
        $this->id = $team_id;
        $this->details = $this->get_details();
    }

    /**
     * Erstellt ein neues Team in der Datenbank
     *
     * Hinweis: Nichtligateams werden stand 12/2020 bei der Anmeldung in der Klasse Turniere erstellt!
     *
     * @param $teamname
     * @param $passwort
     * @param $email
     * @return int Team-ID des neu erstellten Teams
     */
    public static function set_new_team($teamname, $passwort, $email): int
    {
        // Eintrag in teams_liga
        $passwort = password_hash($passwort, PASSWORD_DEFAULT);

        // TeamIDs werden über die Sql-Funktion auto increment vergeben
        $sql = "
                INSERT INTO teams_liga (teamname, passwort, freilose) 
                VALUES (?, ?, 1)
                ";
        db::$db->query($sql, $teamname, $passwort)->log();

        // Eintrag in teams_details
        $team_id = db::$db->get_last_insert_id();
        $sql = "
                INSERT INTO teams_details (team_id) 
                VALUES (?)";
        db::$db->query($sql, $team_id)->log();

        // Eintrag in teams_kontakt
        $sql = "
                INSERT INTO teams_kontakt (team_id, email, public, get_info_mail) 
                VALUES (?, ?, 'Nein', 'Nein')";
        db::$db->query($sql, $team_id, $email)->log();
        return $team_id;
    }

    /**
     * Deaktiviert ein Ligateam, es kann im Ligacenter reaktiviert werden
     *
     * @param int $team_id
     */
    public static function deactivate(int $team_id): void
    {
        $sql = "
                UPDATE teams_liga
                SET aktiv = 'Nein'
                WHERE team_id = ?
                ";
        db::$db->query($sql, $team_id)->log();
    }

    /**
     * Liste der deaktivierten Teams
     *
     * @return array
     */
    public static function get_deactive(): array
    {
        $sql = "
                SELECT * 
                FROM teams_liga 
                WHERE aktiv = 'Nein' AND ligateam = 'Ja' 
                ORDER BY teamname
                ";
        return db::$db->query($sql)->esc()->fetch();
    }

    /**
     * Reaktiviert ein deaktiviertes Team
     *
     * @param int $team_id
     */
    public static function activate(int $team_id): void
    {
        $sql = "
                UPDATE teams_liga 
                SET aktiv = 'Ja' 
                WHERE team_id = ?
                ";
        db::$db->query($sql, $team_id);
    }

    /**
     * Wandelt den Teamnamen in die Teamid um. Gibt 0 zurück, wenn es die TeamID nicht gibt.
     *
     * @param $teamname
     * @return int|null
     */
    public static function name_to_id($teamname): null|int
    {
        $sql = " 
                SELECT team_id 
                FROM teams_liga 
                WHERE teamname = ?
                ";
        return db::$db->query($sql, $teamname)->esc()->fetch_one();
    }

    /**
     * Wandelt die TeamID in den Teamnamen um. Gibt leer zurück, wenn es den Teamnamen nicht gibt.
     *
     * @param $team_id
     * @return string|null
     */
    public static function id_to_name($team_id): null|string
    {
        $sql = "
                SELECT teamname 
                FROM teams_liga 
                WHERE team_id = ?
                ";
        return db::$db->query($sql, $team_id)->esc()->fetch_one();

    }

    /**
     * Prüft ob die TeamID zu einem aktiven Ligateam gehört
     *
     * @param null|int $team_id
     * @return bool
     */
    public static function is_ligateam(null|int $team_id): bool
    {
        $sql = "
                SELECT team_id
                FROM teams_liga
                WHERE team_id = ? AND ligateam = 'Ja' AND aktiv = 'Ja'
                ";
        return db::$db->query($sql, $team_id)->num_rows() > 0;
    }

    /**
     * Gibt ein Array mit allen aktiven Teamnamen zurück (team_id => teamname)
     *
     * @return array
     */
    public static function get_liste(): array
    {
        $sql = "
                SELECT teamname, team_id
                FROM teams_liga
                WHERE ligateam = 'Ja' AND aktiv = 'Ja' 
                ORDER BY teamname
                ";
        return db::$db->query($sql)->esc()->list('teamname', 'team_id');
    }

    /**
     * Array aller IDs von aktiven Ligateams
     *
     * @return array
     */
    public static function get_liste_ids(): array
    {
        $sql = "
                SELECT team_id
                FROM teams_liga
                WHERE ligateam = 'Ja' AND aktiv = 'Ja' 
                ORDER BY team_id 
                ";
        return db::$db->query($sql)->esc()->list('team_id');
    }

    /**
     * Gibt ein Array mit allen Teamdaten aller aktiven Ligateams zurück
     *
     * @return array key: team_id
     */
    public static function get_teams(): array
    {
        $sql = "
                SELECT * 
                FROM teams_liga
                INNER JOIN teams_details
                ON teams_liga.team_id = teams_details.team_id
                WHERE teams_liga.ligateam = 'Ja' AND teams_liga.aktiv = 'Ja'
                ORDER BY teams_liga.teamname
                ";
        return db::$db->query($sql)->esc()->fetch('team_id');
    }

    /**
     * Teamstrafe eintragen
     *
     * @param int $team_id
     * @param string $verwarnung
     * @param int $turnier_id
     * @param string $grund
     * @param int $prozentsatz
     * @param int $saison
     */
    public static function set_strafe(int $team_id, string $verwarnung, int $turnier_id, string $grund,
                                      int $prozentsatz, int $saison = Config::SAISON): void
    {
        $sql = "
                INSERT INTO teams_strafen (team_id, verwarnung, turnier_id, grund, prozentsatz, saison)
                VALUES (?, ?, ?, ?, ?, ?)
                ";
        $params = [$team_id, $verwarnung, $turnier_id, $grund, $prozentsatz, $saison];
        db::$db->query($sql, $params)->log();
    }

    /**
     * Teamstrafe löschen
     *
     * @param int $strafe_id
     */
    public static function unset_strafe(int $strafe_id): void
    {
        $sql = "
                DELETE FROM teams_strafen
                WHERE strafe_id = ?
                ";
        db::$db->query($sql, $strafe_id)->log();
    }

    /**
     * Gibt die Teamstrafen aller Teams zurück
     *
     * @param int $saison
     * @return array
     */
    public static function get_strafen(int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT teams_strafen.*, teams_liga.teamname, turniere_details.ort, turniere_liga.datum 
                FROM teams_strafen
                INNER JOIN teams_liga
                ON teams_liga.team_id = teams_strafen.team_id
                LEFT JOIN turniere_liga
                ON turniere_liga.turnier_id = teams_strafen.turnier_id
                LEFT JOIN turniere_details
                ON turniere_details.turnier_id = teams_strafen.turnier_id
                WHERE teams_strafen.saison = ?
                AND teams_liga.aktiv = 'Ja'
                ORDER BY turniere_liga.datum DESC
                ";
        return db::$db->query($sql, $saison)->esc()->fetch('strafe_id');
    }

    /**
     * Ein Array aller Daten eines Teams, welche man brauchen könnte
     *
     * @return array
     */
    public function get_details(): array
    {
        $sql = "
                SELECT *  
                FROM teams_liga 
                INNER JOIN teams_details
                ON teams_details.team_id = teams_liga.team_id
                WHERE teams_liga.team_id = $this->id
                ";
        return db::$db->query($sql)->esc()->fetch_row();
    }

    /**
     * Verändert den Teamnamen
     *
     * @param string $name
     */
    public function set_name(string $name): void
    {
        $sql = "
                UPDATE teams_liga
                SET teamname = ?
                WHERE team_id = $this->id
                ";
        db::$db->query($sql, $name)->log();
    }

    /**
     * Gibt Anzahl der Freilose des Teams zurück.
     *
     * @return int
     */
    public function get_freilose(): int
    {
        $sql = "
                SELECT freilose
                FROM teams_liga
                WHERE team_id = $this->id
                ";
        return db::$db->query($sql)->esc()->fetch_one();
    }

    /**
     * Setzt die Anzahl der Freilose eines Teams.
     *
     * @param int $anzahl
     */
    public function set_freilose(int $anzahl): void
    {
        $sql = "
                UPDATE teams_liga
                SET freilose = ?
                WHERE team_id = $this->id
                ";
        db::$db->query($sql, $anzahl)->log();
    }

    /**
     * Fügt ein Freilos hinzu.
     *
     * @param $team_id
     */
    public static function add_freilos($team_id): void
    {
        $sql = "
                UPDATE teams_liga 
                SET freilose = freilose + 1 
                WHERE team_id = ?
                ";
        db::$db->query($sql, $team_id)->log();
    }

    /**
     * Setzt das zweite Freilos mit Zeitstempel in der Datenbank.
     */
    public function set_zweites_freilos(): void
    {
        $sql = "
                UPDATE teams_liga
                SET freilose = freilose + 1, zweites_freilos = ?
                WHERE team_id = $this->id
                ";
        db::$db->query($sql, date("Y-m-d"))->log();
        Helper::log('schirifreilos.log', "$this->id hat für zwei Schiris ein Freilos erhalten.");
        MailBot::mail_zweites_freilos($this);
    }

    /**
     * Hat das Team in dieser Saison schon ein zweites Freilos für zwei Schiris erhalten?
     *
     * @return bool
     */
    public function check_schiri_freilos_erhalten(): bool
    {
        return (self::static_check_schiri_freilos_erhalten($this->details['zweites_freilos']));
    }

    public static function static_check_schiri_freilos_erhalten($zweites_freilos): bool
    {
        $erhalten_am = empty($zweites_freilos)
            ? 0
            : strtotime($zweites_freilos);
        return $erhalten_am >= strtotime(Config::SAISON_ANFANG);
    }

    /**
     * Ist das Team berechtigt ein zweites Freilos für zwei Schiris zu bekommen?
     *
     * @return bool
     */
    public function check_schiri_freilos_erhaltbar(): bool
    {
        // False, wenn die neue Saison noch nicht begonnen hat
        if (time() < strtotime(Config::SAISON_ANFANG)){
            return false;
        }

        // False, wenn schon ein Schiri-Freilos in der Saison erhalten wurde.
        if ($this->check_schiri_freilos_erhalten()){
            return false;
        }

        // Zwei oder mehr Schiris im Kader?
        $sql = "
                SELECT count(schiri)
                FROM spieler
                WHERE schiri >= ?
                AND team_id = $this->id
                AND letzte_saison = ?
                ";

        return db::$db->query($sql, Config::SAISON, Config::SAISON)->fetch_one() >= 2;
    }

    /**
     *  Überprüft alle Teams und setzt die Schiri-Freilose.
     */
    public static function set_schiri_freilose(): void {
        $team_ids = self::get_liste_ids();
        foreach ($team_ids as $id) {
            (new Team($id))->set_schiri_freilos();
        }
    }

    /**
     *  Setzt das zweite Schiri-Freilos, falls das Team berechtigt ist.
     */
    public function set_schiri_freilos(): void {
       if ($this->check_schiri_freilos_erhaltbar()){
                Html::info("Das Team '" . $this->details['teamname'] . "' hat ein zweites Freilos erhalten.");
                $this->set_zweites_freilos();
            }
    }

    /**
     * Schreibt Teamdetails in die Datenbank
     *
     * SQL-Tabellenspaltenname
     * @param string $spalten_name
     * Wert der in die Datenbank für das Team eingefügt werden soll
     * @param mixed $value
     */
    public function set_detail(string $spalten_name, mixed $value): void
    {
        // Validieren, ob der Spaltenname ein echter Spaltenname ist
        $spalten_namen = db::$db->query("SHOW FIELDS FROM teams_details")->list('Field');
        if (!in_array($spalten_name, $spalten_namen, true)) {
            trigger_error("Ungültiger Spaltenname", E_USER_ERROR);
        }
        $spalten_name = "`" . $spalten_name . "`";

        $sql = "
                UPDATE teams_details
                SET $spalten_name = ?
                WHERE team_id = $this->id
                ";
        db::$db->query($sql, $value)->log();
    }

    /**
     * Eine Liste aller TurnierIDs zu dem die TeamID angemeldet ist
     *
     * @return array
     */
    public function get_turniere_angemeldet(): array
    {
        $sql = "
                SELECT turnier_id, liste 
                FROM turniere_liste 
                WHERE team_id = $this->id
                ";
        return db::$db->query($sql)->list('liste', 'turnier_id');
    }

    /**
     * Teamfoto löschen
     */
    public function delete_foto(): void
    {
        // Foto löschen
        if (file_exists($this->details['teamfoto'])) {
            unlink($this->details['teamfoto']);
        }
        // Fotolink aus der Datenbank entfernen
        $sql = "
                UPDATE teams_details
                SET teamfoto = ''
                WHERE team_id = $this->id
                ";
        db::$db->query($sql)->log();
    }

    /**
     * Set nach dem Login die Session des Teamcenters.
     *
     * @param Team $team
     */
    public static function set_team_session(Team $team): void
    {
        $_SESSION['logins']['team']['id'] = $team->id;
        $_SESSION['logins']['team']['name'] = $team->details['teamname'];
        $_SESSION['logins']['team']['block'] = Tabelle::get_team_block($team->id);
    }

    /**
     * Login Teamcenter
     *
     * @param string $teamname
     * @param string $passwort
     * @return bool
     */
    public static function login(string $teamname, string $passwort): bool
    {
        // Existenz prüfen
        $team_id = self::name_to_id($teamname);

        if (!self::is_ligateam($team_id)) {
            Html::error("Falscher Loginname");
            Helper::log(Config::LOG_LOGIN, "Falscher TC-Login | Teamname: " . $teamname);
            return false;
        }

        $team = new Team ($team_id);
        // Passwort prüfen
        if (password_verify($passwort, $team->details['passwort'])) {
            self::set_team_session($team);
            Helper::log(Config::LOG_LOGIN, "Erfolgreich       | Teamname: " . $teamname);

            if (empty($team->details['trikot_farbe_1'])) {
                $link = Html::link("tc_teamdaten_aendern.php", ' Link.', icon: "launch");
                Html::info("Ihr könnt nun eure Trikotfarben hinzufügen - " . $link, ' ', esc: false);
            }
            if (empty($team->details['teamfoto'])) {
                $link = Html::link("../teamcenter/tc_teamdaten_aendern.php", ' Link.', icon: "launch");
                Html::info("Hier könnt ihr noch ein Teamfoto hochladen - " . $link, ' ', esc: false);
            }

            return true;
        }

        // Passwort falsch
        Helper::log(Config::LOG_LOGIN, "Falsches Passwort | Teamname: " . $teamname);
        Html::error("Falsches Passwort");
        return false;
    }

    /**
     * Setzt Passwort des Teams
     *
     * @param string $passwort
     */
    public function set_passwort(string $passwort): void
    {
        // Passwort hashen
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
        if (!is_string($passwort)) {
            trigger_error("set_passwort fehlgeschlagen.", E_USER_ERROR);
        }

        // Befindet sich das Team im Teamcenter ihr Passwort geändert?
        $pw_geaendert = (Helper::$teamcenter) ? 'Ja' : 'Nein';

        // Passwort in die Datenbank
        $sql = "
                UPDATE teams_liga
                SET passwort = ?, passwort_geaendert = ?
                WHERE team_id = $this->id
                ";
        db::$db->query($sql, $passwort_hash, $pw_geaendert)->log();
    }

    /**
     * Setzt die Wertigkeit vor dem benannten Spieltag
     * 
     * @param int $spieltag
     */
    public function set_wertigkeit(int $spieltag): void
    {
        $this->wertigkeit = Tabelle::get_team_wertigkeit($this->id, $spieltag - 1);
    }

    /**
     * Setzt den Teamblock vor dem benannten Spieltag
     * 
     * @param int $spieltag
     */
    public function set_tblock(int $spieltag): void
    {
        $this->tblock = Tabelle::get_team_block($this->id, $spieltag - 1);
    }

    /**
     * Setzte den Teamrang vor dem benannten Spieltag
     * 
     * @param int $spieltag
     */
    public function set_rang(int $spieltag): void
    {
        $this->rang = Tabelle::get_team_rang($this->id, $spieltag - 1);
    }

    /**
     * Setzte die Wartelisteposition des Teams auf einem Turnier
     * 
     * @param int $spieltag
     */
    public function set_position_warteliste(int $pos): void
    {
        $this->position_warteliste = $pos;
    }

    /**
     * Gibt die Teamwertigkeit
     * 
     * @return int
     */
    public function get_wertigkeit(): int
    {
        return $this->wertigkeit;
    }

    /**
     * Gibt den Teamblock
     */
    public function get_tblock(): string
    {
        return $this->tblock;
    }

    /**
     * Gibt den Teamrang
     */
    public function get_rang(): int
    {
        return $this->rang;
    }

    /**
     * Gibt die Wartelisteposition
     */
    public function get_warteliste_postition(): int
    {
        return $this->position_warteliste;
    }
}