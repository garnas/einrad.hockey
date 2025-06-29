<?php

/**
 *
 */
class nSpieler
{
    public int $spieler_id;
    public ?int $team_id;
    public ?String $teamname;
    public ?int $letzte_saison;
    public ?string $vorname;
    public ?string $nachname;
    public ?int $jahrgang;
    public ?string $geschlecht;
    public ?int $schiri = NULL;
    public ?string $junior = NULL;
    public ?string $timestamp;

    public bool $error = false;



    /**
     * Spieler constructor.
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
     * @param int $team_id
     * @param int $saison
     * @return nSpieler[]
     */
    public static function get_kader(int $team_id, int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT spieler.* , teams_liga.teamname
                FROM spieler 
                LEFT JOIN teams_liga on spieler.team_id = teams_liga.team_id
                WHERE spieler.team_id = ?
                AND spieler.letzte_saison = ?
                ";
        return db::$db->query($sql, $team_id, $saison)->fetch_objects(__CLASS__, key:'spieler_id');
    }

}