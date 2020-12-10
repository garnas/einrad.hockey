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
    public int $team_id;

    /**
     * Team constructor.
     * @param $team_id
     */
    function __construct($team_id)
    {
        $this->team_id = $team_id;
    }

    /**
     * Erstellt ein neues Team in der Datenbank
     *
     * Hinweis: Nichtligateams werden stand 12/2020 bei der Anmeldung in der Klasse Turniere erstellt!
     *
     * @param $teamname
     * @param $passwort
     * @param $email
     */
    public static function create_new_team($teamname, $passwort, $email)
    {
        // Eintrag in teams_liga
        $passwort = password_hash($passwort, PASSWORD_DEFAULT);

        // TeamIDs werden über die Sql-Funktion auto increment vergeben
        $team_id = db::get_auto_increment("teams_liga");

        $sql =  "
                INSERT INTO teams_liga (teamname, passwort, freilose) 
                VALUES ('$teamname','$passwort',2)
                ";
        db::write($sql);

        // Eintrag in teams_details
        $sql =  "
                INSERT INTO teams_details (team_id) 
                VALUES ('$team_id')";
        db::write($sql);

        // Eintrag in teams_kontakt
        $sql =  "
                INSERT INTO teams_kontakt (team_id, email, public, get_info_mail) 
                VALUES ('$team_id','$email','Ja','Nein')";
        db::write($sql);
    }

    /**
     * Deaktiviert ein Ligateam, es kann im Ligacenter reaktiviert werden
     *
     * @param $team_id
     */
    public static function deactivate_team($team_id)
    {
        $sql =  "
                UPDATE teams_liga
                SET aktiv='Nein'
                WHERE team_id='$team_id'
                ";
        db::write($sql);
    }

    /**
     * Liste der deaktivierten Teams
     *
     * @return array
     */
    public static function get_deactive_teams(): array
    {
        $sql =  "
                SELECT * 
                FROM teams_liga 
                WHERE aktiv = 'Nein' AND ligateam = 'Ja' 
                ORDER BY teamname
                ";
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)) {
            array_push($return, $x);
        }
        return db::escape($return);
    }

    /**
     * Reaktiviert ein deaktiviertes Team
     *
     * @param $team_id
     */
    public static function activate_team($team_id)
    {
        $sql =  "
                UPDATE teams_liga 
                SET aktiv = 'Ja' 
                WHERE team_id = '$team_id'
                ";
        db::write($sql);
    }

    /**
     * Wandelt den Teamnamen in die Teamid um. Gibt 0 zurück, wenn es die TeamID nicht gibt.
     *
     * @param $teamname
     * @return int
     */
    public static function teamname_to_teamid($teamname): int
    {
        $teamname = htmlspecialchars_decode($teamname);
        $sql =  " 
                SELECT team_id 
                FROM teams_liga 
                WHERE teamname = '$teamname'
                ";
        $result = mysqli_fetch_assoc(db::read($sql));
        return $result['team_id'] ?? 0;
    }

    /**
     * Wandelt die TeamID in den Teamnamen um. Gibt leer zurück, wenn es den Teamnamen nicht gibt.
     *
     * @param $team_id
     * @return string
     */
    public static function teamid_to_teamname($team_id): string
    {
        $sql =  "
                SELECT teamname 
                FROM teams_liga 
                WHERE team_id = '$team_id'
                ";
        $result = mysqli_fetch_assoc(db::read($sql));
        return db::escape($result['teamname'] ?? '');
    }

    /**
     * Prüft ob die TeamID zu einem aktiven Ligateam gehört
     *
     * @param $team_id
     * @return bool
     */
    public static function is_ligateam($team_id): bool
    {
        $sql =  "
                SELECT team_id
                FROM teams_liga
                WHERE team_id = '$team_id' AND ligateam='Ja' AND aktiv='Ja'
                ";
        $result = mysqli_fetch_assoc(db::read($sql));
        if (!empty($result['team_id'])) {
            return true;
        }
        return false;
    }

    /**
     * Gibt ein Array mit allen aktiven Teamnamen zurück
     *
     * @return array
     */
    public static function get_ligateams_name(): array
    {
        $sql =  "
                SELECT teamname
                FROM teams_liga
                WHERE ligateam = 'Ja' AND aktiv = 'Ja' 
                ORDER BY teamname
                ";
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)) {
            array_push($return, $x['teamname']);
        }
        return db::escape($return);
    }

    /**
     * Array aller IDs von aktiven Ligateams
     *
     * @return array
     */
    public static function get_ligateams_id(): array
    {
        $sql =  "
                SELECT team_id
                FROM teams_liga
                WHERE ligateam = 'Ja' AND aktiv = 'Ja' 
                ORDER BY RAND()
                ";
        $result = db::read($sql);
        $return = array();
        while ($eintrag = mysqli_fetch_assoc($result)) {
            array_push($return, $eintrag['team_id']);
        }
        return db::escape($return);
    }

    /**
     * Gibt ein Array mit allen Teamdaten aller aktiven Ligateams zurück
     *
     * @return array
     */
    public static function get_teamdata_all_teams(): array
    {
        $sql =  "
                SELECT * 
                FROM teams_liga
                INNER JOIN teams_details
                ON teams_liga.team_id = teams_details.team_id
                WHERE teams_liga.ligateam = 'Ja' AND teams_liga.aktiv = 'Ja'
                ORDER BY teams_liga.teamname
                ";
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)) {
            $return[$x['team_id']] = $x;
        }
        return db::escape($return);
    }

    /**
     * Fügt ein Freilos hinzu
     *
     * @param $team_id
     */
    public static function add_freilos($team_id)
    {
        $sql =  "
                UPDATE teams_liga 
                SET freilose = freilose + 1 
                WHERE team_id = '$team_id'
                ";
        db::write($sql);
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
    public static function strafe_eintragen(int $team_id, string $verwarnung, int $turnier_id, string $grund, int $prozentsatz, $saison = Config::SAISON)
    {
        $sql =  "
                INSERT INTO teams_strafen (team_id, verwarnung, turnier_id, grund, prozentsatz, saison)
                VALUES ('$team_id','$verwarnung','$turnier_id','$grund','$prozentsatz', '$saison'
                ";
        db::write($sql);
    }

    /**
     * Teamstrafe löschen
     *
     * @param int $strafe_id
     */
    public static function strafe_loeschen(int $strafe_id)
    {
        $sql =  "
                DELETE FROM teams_strafen
                WHERE strafe_id = '$strafe_id'
                ";
        db::write($sql);
    }

    /**
     * Gibt die Teamstrafen aller Teams zurück
     *
     * @return array
     */
    public static function get_strafen_all_teams(): array
    {
        $sql =  "
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
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)) {
            $return[$x['strafe_id']] = $x;
        }
        return db::escape($return);
    }

    /**
     * Ein Array aller Daten eines Teams, welche man brauchen könnte
     *
     * @return array
     */
    function get_teamdaten(): array
    {
        $team_id = $this->team_id;
        $sql =  "
                SELECT *  
                FROM teams_liga 
                INNER JOIN teams_details
                ON teams_details.team_id=teams_liga.team_id
                WHERE teams_liga.team_id='$team_id'
                ";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        return db::escape($result);
    }

    /**
     * Verändert den Teamnamen
     *
     * @param string $name
     */
    function set_teamname(string $name)
    {
        $team_id = $this->team_id;
        $sql =  "
                UPDATE teams_liga
                SET teamname = '$name'
                WHERE team_id='$team_id'
                ";
        db::write($sql);
    }

    /**
     * Gibt Passworthash zurück
     *
     * @return string|bool
     */
    function get_passwort(): string|bool
    {
        $team_id = $this->team_id;
        $sql =  "
                SELECT passwort
                FROM teams_liga
                WHERE team_id='$team_id'
                ";
        db::debug($sql);
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        return $result['passwort'];
    }

    /**
     * Setzt Passwort
     *
     * @param string $passwort
     * @param string $pw_geaendert
     */
    function set_passwort(string $passwort, string $pw_geaendert = 'Ja')
    {
        $team_id = $this->team_id;
        $passwort = password_hash($passwort, PASSWORD_DEFAULT);
        $sql =  "
                UPDATE teams_liga
                SET passwort = '$passwort', passwort_geaendert = '$pw_geaendert'
                WHERE team_id='$team_id'
                ";
        db::write($sql);
    }

    /**
     * Gibt Anzahl der Freilose des Teams zurück
     *
     * @return int
     */
    function get_freilose(): int
    {
        $team_id = $this->team_id;
        $sql =  "
                SELECT freilose
                FROM teams_liga
                WHERE team_id='$team_id'
                ";
        $result = db::read($sql);
        $result = mysqli_fetch_assoc($result);
        return $result['freilose'];
    }

    /**
     * Setzt die Anzahl der Freilose eines Teams
     *
     * @param $anzahl
     */
    function set_freilose($anzahl)
    {
        $team_id = $this->team_id;
        $sql =  "
                UPDATE teams_liga
                SET freilose='$anzahl'
                WHERE team_id='$team_id'
                ";
        db::write($sql);
    }

    /**
     * Schreibt Teamdetails in die Datenbank
     *
     * SQL-Tabellenspaltenname
     * @param string $entry
     * Wert der in die Datenbank für das Team eingefügt werden soll
     * @param mixed $value
     */
    function set_team_detail(string $entry, mixed $value)
    {
        $team_id = $this->team_id;
        $sql =  "
                UPDATE teams_details
                SET $entry = '$value'
                WHERE team_id='$team_id'
                ";
        db::write($sql);
    }

    /**
     * Eine Liste aller TurnierIDs zu dem die TeamID angemeldet ist
     *
     * @return array
     */
    function get_turniere_angemeldet(): array
    {
        $team_id = $this->team_id;
        $sql =  "
                SELECT turnier_id, liste 
                FROM turniere_liste 
                WHERE team_id = $team_id
                ";
        $result = db::read($sql);
        $return = array();
        while ($x = mysqli_fetch_assoc($result)) {
            $return[$x['turnier_id']] = $x['liste'];
        }
        return $return;
    }

    /**
     * Pfad des Teamfotos in die Datenbank schreiben
     *
     * Pfad des Teamfotos
     * @param string $target
     */
    function set_teamfoto(string $target)
    {
        $team_id = $this->team_id;
        $sql =  "
                UPDATE teams_details 
                SET teamfoto = '$target' 
                WHERE team_id='$team_id'
                ";
        db::write($sql);
    }

    /**
     * Teamfoto löschen
     *
     * Pfad des Teamfotos
     * @param string $target
     */
    function delete_teamfoto(string $target)
    {
        $team_id = $this->team_id;
        // Foto löschen
        if (file_exists($target)) {
            unlink($target);
        }
        // Fotolink aus der Datenbank entfernen
        $sql =  "
                UPDATE teams_details
                SET teamfoto = ''
                WHERE team_id='$team_id'
                ";
        db::write($sql);
    }
}