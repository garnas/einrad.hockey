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
     * Team constructor.
     * @param $team_id
     */
    function __construct($team_id)
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
                VALUES (?, ?, 2)
                ";
        dbi::$db->query($sql, $teamname, $passwort)->log();

        // Eintrag in teams_details
        $team_id = dbi::$db->get_last_insert_id();
        $sql = "
                INSERT INTO teams_details (team_id) 
                VALUES (?)";
        dbi::$db->query($sql, $team_id)->log();

        // Eintrag in teams_kontakt
        $sql = "
                INSERT INTO teams_kontakt (team_id, email, public, get_info_mail) 
                VALUES (?, ?, 'Ja', 'Nein')";
        dbi::$db->query($sql, $team_id, $email)->log();
        return $team_id;
    }

    /**
     * Deaktiviert ein Ligateam, es kann im Ligacenter reaktiviert werden
     *
     * @param $team_id
     */
    public static function deactivate_team(int $team_id)
    {
        $sql = "
                UPDATE teams_liga
                SET aktiv = 'Nein'
                WHERE team_id = ?
                ";
        dbi::$db->query($sql, $team_id)->log();
    }

    /**
     * Liste der deaktivierten Teams
     *
     * @return array
     */
    public static function get_deactive_teams(): array
    {
        $sql = "
                SELECT * 
                FROM teams_liga 
                WHERE aktiv = 'Nein' AND ligateam = 'Ja' 
                ORDER BY teamname
                ";
        return dbi::$db->query($sql)->esc()->fetch();
    }

    /**
     * Reaktiviert ein deaktiviertes Team
     *
     * @param $team_id
     */
    public static function activate_team(int $team_id)
    {
        $sql = "
                UPDATE teams_liga 
                SET aktiv = 'Ja' 
                WHERE team_id = ?
                ";
        dbi::$db->query($sql, $team_id);
    }

    /**
     * Wandelt den Teamnamen in die Teamid um. Gibt 0 zurück, wenn es die TeamID nicht gibt.
     *
     * @param $teamname
     * @return int|null
     */
    public static function teamname_to_teamid($teamname): null|int
    {
        $sql = " 
                SELECT team_id 
                FROM teams_liga 
                WHERE teamname = ?
                ";
        return dbi::$db->query($sql, $teamname)->esc()->fetch_one();
    }

    /**
     * Wandelt die TeamID in den Teamnamen um. Gibt leer zurück, wenn es den Teamnamen nicht gibt.
     *
     * @param $team_id
     * @return string|null
     */
    public static function teamid_to_teamname($team_id): null|string
    {
        $sql = "
                SELECT teamname 
                FROM teams_liga 
                WHERE team_id = ?
                ";
        return dbi::$db->query($sql, $team_id)->esc()->fetch_one();

    }

    /**
     * Prüft ob die TeamID zu einem aktiven Ligateam gehört
     *
     * @param $team_id
     * @return bool
     */
    public static function is_ligateam($team_id): bool
    {
        $sql = "
                SELECT team_id
                FROM teams_liga
                WHERE team_id = ? AND ligateam = 'Ja' AND aktiv = 'Ja'
                ";
        return dbi::$db->query($sql, $team_id)->num_rows() > 0;
    }

    /**
     * Gibt ein Array mit allen aktiven Teamnamen zurück
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
        return dbi::$db->query($sql)->esc()->list('teamname', 'team_id');
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
                "; //TODO Früher nach RAND() für Abhandlung Ligabot, jetzt besser shuffle() einbauen
        return dbi::$db->query($sql)->esc()->list('team_id');
    }

    /**
     * Gibt ein Array mit allen Teamdaten aller aktiven Ligateams zurück
     *
     * @return array key: team_id
     */
    public static function get_teamdata_all_teams(): array
    {
        $sql = "
                SELECT * 
                FROM teams_liga
                INNER JOIN teams_details
                ON teams_liga.team_id = teams_details.team_id
                WHERE teams_liga.ligateam = 'Ja' AND teams_liga.aktiv = 'Ja'
                ORDER BY teams_liga.teamname
                ";
        return dbi::$db->query($sql)->esc()->fetch('team_id');
    }

    /**
     * Fügt ein Freilos hinzu
     *
     * @param $team_id
     */
    public static function add_freilos($team_id)
    {
        $sql = "
                UPDATE teams_liga 
                SET freilose = freilose + 1 
                WHERE team_id = ?
                ";
        dbi::$db->query($sql, $team_id)->log();
    }

    /**
     * Teamstrafe eintragen
     *
     * @param int $team_id
     * @param string $verwarnung
     * @param int $turnier_id
     * @param string $grund
     * @param int $prozentsatz
     * @param string $saison
     */
    public static function strafe_eintragen(int $team_id, string $verwarnung, int $turnier_id, string $grund,
                                            int $prozentsatz, $saison = Config::SAISON)
    {
        $sql = "
                INSERT INTO teams_strafen (team_id, verwarnung, turnier_id, grund, prozentsatz, saison)
                VALUES (?, ?, ?, ?, ?, ?)
                ";
        $params = [$team_id, $verwarnung, $turnier_id, $grund, $prozentsatz, $saison];
        dbi::$db->query($sql, $params)->log();
    }

    /**
     * Teamstrafe löschen
     *
     * @param int $strafe_id
     */
    public static function strafe_loeschen(int $strafe_id)
    {
        $sql = "
                DELETE FROM teams_strafen
                WHERE strafe_id = ?
                ";
        dbi::$db->query($sql, $strafe_id)->log();
    }

    /**
     * Gibt die Teamstrafen aller Teams zurück
     *
     * @return array
     */
    public static function get_strafen(): array
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
                WHERE teams_strafen.saison = '" . Config::SAISON . "'
                AND teams_liga.aktiv = 'Ja'
                ORDER BY turniere_liga.datum DESC
                ";
        return dbi::$db->query($sql)->esc()->fetch('strafe_id');
    }

    /**
     * Ein Array aller Daten eines Teams, welche man brauchen könnte
     *
     * @return array
     */
    function get_details(): array
    {
        $sql = "
                SELECT *  
                FROM teams_liga 
                INNER JOIN teams_details
                ON teams_details.team_id = teams_liga.team_id
                WHERE teams_liga.team_id = $this->id
                ";
        return dbi::$db->query($sql)->esc()->fetch_row();
    }

    /**
     * Verändert den Teamnamen
     *
     * @param string $name
     */
    function set_teamname(string $name)
    {
        $sql = "
                UPDATE teams_liga
                SET teamname = ?
                WHERE team_id = $this->id
                ";
        dbi::$db->query($sql, $name)->log();
    }

    /**
     * Gibt Passworthash zurück
     *
     * @return string|bool
     */
    function get_passwort(): string|bool
    {
        $sql = "
                SELECT passwort
                FROM teams_liga
                WHERE team_id = $this->id
                ";
        return dbi::$db->query($sql)->log()->fetch_one();
    }

    /**
     * Setzt Passwort
     *
     * @param string $passwort
     * @param string $pw_geaendert
     */
    function set_passwort(string $passwort, string $pw_geaendert = 'Ja')
    {
        $passwort = password_hash($passwort, PASSWORD_DEFAULT);
        $sql = "
                UPDATE teams_liga
                SET passwort = ?, passwort_geaendert = ?
                WHERE team_id = $this->id
                ";
        dbi::$db->query($sql, $passwort, $pw_geaendert)->log();
    }

    /**
     * Gibt Anzahl der Freilose des Teams zurück
     *
     * @return int
     */
    function get_freilose(): int
    {
        $sql = "
                SELECT freilose
                FROM teams_liga
                WHERE team_id = $this->id
                ";
        return dbi::$db->query($sql)->log()->fetch_one();
    }

    /**
     * Setzt die Anzahl der Freilose eines Teams
     *
     * @param $anzahl
     */
    function set_freilose(int $anzahl)
    {
        $sql = "
                UPDATE teams_liga
                SET freilose = ?
                WHERE team_id = $this->id
                ";
        dbi::$db->query($sql, $anzahl)->log();
    }

    /**
     * Schreibt Teamdetails in die Datenbank
     *
     * SQL-Tabellenspaltenname
     * @param string $spalten_name
     * Wert der in die Datenbank für das Team eingefügt werden soll
     * @param mixed $value
     */
    function set_detail(string $spalten_name, mixed $value)
    {
        // Validieren, ob der Spaltenname ein echter Spaltenname ist
        $spalten_namen = dbi::$db->query("SHOW FIELDS FROM teams_details")->list('Field');
        if (!in_array($spalten_name, $spalten_namen)) die("Ungültiger Spaltenname");
        $spalten_name = "`" . $spalten_name . "`";

        $sql = "
                UPDATE teams_details
                SET $spalten_name = ?
                WHERE team_id = $this->id
                ";
        dbi::$db->query($sql, $value)->log();
    }

    /**
     * Eine Liste aller TurnierIDs zu dem die TeamID angemeldet ist
     *
     * @return array
     */
    function get_turniere_angemeldet(): array
    {
        $sql = "
                SELECT turnier_id, liste 
                FROM turniere_liste 
                WHERE team_id = $this->id
                ";
        return dbi::$db->query($sql)->list('liste', 'turnier_id');
    }

    /**
     * Teamfoto löschen
     *
     */
    function delete_teamfoto()
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
        dbi::$db->query($sql)->log();
    }
}