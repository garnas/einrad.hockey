<?php

/**
 * Class Turnier
 *
 * Alles für die Verwaltung und zum Anzeigen von Turnieren
 */
class nTurnier
{
    public int $turnier_id;
    public ?string $tname;
    public ?int $ausrichter;
    public ?string $art;
    public ?string $block;
    public ?string $tblock_fixed;
    public ?string $datum;
    public ?int $unix;
    public ?int $spieltag;
    public ?string $phase;
    public ?string $spielplan_vorlage;
    public ?string $spielplan_datei;
    public ?int $saison;

    public bool $error = false;

    /**
     * Turnier constructor.
     */
    public function __construct($esc = true)
    {
        if ($esc) {
            foreach (get_object_vars($this) as $name => $value) {
                $this->$name = db::escape($value);
            }
        }
    }

    /**
     * @param int $id
     * @return nTurnier
     */
    public static function get(int $id): nTurnier
    {
        $sql = "
        SELECT turniere_liga.*, turniere_details.*
            FROM turniere_liga
            LEFT JOIN turniere_details ON turniere_liga.turnier_id = turniere_details.turnier_id
            WHERE turniere_liga.turnier_id = ?
        ";
        return db::$db->query($sql, $id)->fetch_object(__CLASS__) ?? new nTurnier();
    }

    public static function get_all(): array
    {
        $sql = "
            SELECT turniere_liga.*, turniere_details.*
            FROM turniere_liga
            LEFT JOIN turniere_details ON turniere_liga.turnier_id = turniere_details.turnier_id
        ";
        return db::$db->query($sql)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * @param string $phase
     * @param bool $equal
     * @param bool $asc
     * @param int $saison
     * @return nTurnier[]
     */
    public static function get_turniere(
        string $phase,
        bool $equal = true,
        bool $asc = true,
        int $saison = Config::SAISON
    ): array {
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
        return db::$db->query($sql, $phase, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * @param int $saison
     * @return nTurnier[]
     */
    public static function get_finalturniere(int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT *
                FROM turniere_liga
                LEFT JOIN turniere_details ON turniere_liga.turnier_id = turniere_details.turnier_id
                WHERE saison = ?
                AND art LIKE 'final';
        ";
        return db::$db->query($sql, $saison)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Get Turniere, welche ein Team ausrichtet.
     *
     * @param int $team_id Ausrichter
     * @return nTurnier[]
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
        return db::$db->query($sql, $team_id)->fetch_objects(__CLASS__, key: 'turnier_id');
    }

    /**
     * Turnierdetails von nur einem Turnier erhalten
     *
     * @return nTurnier
     */
    public function get_details(): nTurnier
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
        return db::$db->query($sql)->fetch_object(__CLASS__) ?? new nTurnier();
    }

    /**
     * @return int
     */
    public function get_turnier_id(): int
    {
        return $this->turnier_id;
    }

    /**
     * @return string
     */
    public function get_datum(): string
    {
        return $this->datum;
    }

    /**
     * @return int
     */
    public function get_unix(): int
    {
        return strtotime($this->datum);
    }

    /**
     * @return int
     */
    public function get_ausrichter(): int
    {
        return $this->ausrichter;
    }

    /**
     * @return string
     */
    public function get_art(): string
    {
        return $this->art;
    }

    /**
     * @return int
     */
    public function get_spieltag()
    {
        return $this->spieltag;
    }

    /**
     * @return string
     */
    public function get_phase()
    {
        return $this->phase;
    }
}
