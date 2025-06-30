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
    public string $teamname;

    /**
     * Werden nur bei Bedarf gesetzt.
     * Dazu müssen dann die entsprechenden Setter aufgerufen werden.
     */
    public ?int $wertigkeit;
    public ?string $tblock;
    public ?int $rang;
    public ?int $position_warteliste;
    public ?string $freilos_gesetzt;

    private static array $cache_id_to_name;
    private static array $cache_details;

    /**
     * Team constructor.
     * @param $team_id
     */
    public function __construct($team_id, $skip_init = false)
    {
        $this->id = $team_id;
        if (!$skip_init) {
            $this->details = $this->get_details();
            $this->teamname = self::id_to_name($team_id);
        }
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
     * @param int $team_id
     * @param int $saison
     * @return string|null
     */
    public static function id_to_name(int $team_id, int $saison = Config::SAISON): null|string
    {

        if (isset(self::$cache_id_to_name[$team_id])) {
            return self::$cache_id_to_name[$team_id];
        }
            $sql = "
                SELECT teamname 
                FROM teams_liga 
                WHERE team_id = ?
            ";
            $params = [$team_id];

        if ($saison != Config::SAISON) {
            $teamname = self::id_to_historic_name($team_id, $saison);
            if (empty($teamname)) {
                $teamname = db::$db->query($sql, $params)->esc()->fetch_one();
            }
        } else {
            $teamname = db::$db->query($sql, $params)->esc()->fetch_one();
        }

        self::$cache_id_to_name[$team_id] = $teamname;
        return $teamname;
    }

    /**
     * Wandelt die TeamID in den Teamnamen aus der teams_historic Tabelle um. Gibt leer zurück, wenn es den Teamnamen nicht gibt.
     *
     * @param int $team_id
     * @param int $saison
     * @return string|null
     */
    public static function id_to_historic_name(int $team_id, int $saison = Config::SAISON): null|string
    {
        $sql = "
            SELECT name
            FROM teams_name_historic
            WHERE team_id = ?
            AND saison = ?
        ";
        $params = [$team_id, $saison];
        return db::$db->query($sql, $params)->esc()->fetch_one();
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
     * Hinterlegt, dass das Team den Terminplaner nutzt.
     */
    public function set_terminplaner(): void
    {
        $sql = "
                UPDATE teams_liga 
                SET terminplaner = 'Ja'
                WHERE team_id = $this->id
                ";
        db::$db->query($sql)->log();
    }

    /**
     * True, wenn das Team bereits einen Terminplaner-Account hat.
     *
     * @return bool
     */
    public function check_terminplaner(): bool
    {
        $sql = "
                SELECT terminplaner 
                FROM teams_liga 
                WHERE team_id = $this->id
                ";
        return db::$db->query($sql)->fetch_one() === 'Ja';
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
        if (isset(self::$cache_details[$this->id])) {
            return self::$cache_details[$this->id];
        }

        $sql = "
                SELECT *  
                FROM teams_liga 
                INNER JOIN teams_details
                ON teams_details.team_id = teams_liga.team_id
                WHERE teams_liga.team_id = $this->id
                ";
        self::$cache_details[$this->id] = db::$db->query($sql)->esc()->fetch_row();
        return self::$cache_details[$this->id];
    }

    /**
     * Gibt die Teamwertigkeit
     * 
     * @return null|int
     */
    public function get_wertigkeit(): null|int
    {
        return $this->wertigkeit;
    }

    /**
     * Setzt die Wertigkeit vor dem benannten Spieltag
     * 
     * @param int $spieltag
     */
    public function set_wertigkeit(int $spieltag, int $saison = Config::SAISON): void
    {
        $this->wertigkeit = Tabelle::get_team_wertigkeit($this->id, $spieltag - 1, $saison);
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
     * Setzt die Information, ob ein Freilos gesetzt wurde
     * 
     * @param string $freilos_gesetzt
     */
    public function set_freilos_gesetzt(string $freilos_gesetzt): void
    {
        $this->freilos_gesetzt = $freilos_gesetzt;
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
     * Gibt den Teamblock
     * 
     * @return null|string
     */
    public function get_tblock(): null|string
    {
        return $this->tblock;
    }

}