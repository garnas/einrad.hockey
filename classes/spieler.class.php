<?php

/**
 * Class Spieler
 */
class Spieler
{
    /**
     * Eindeutige ID eines Spielers
     * @var int
     */
    public int $id;
    public array $details;

    /**
     * Spieler constructor.
     * @param $spieler_id
     */
    function __construct(int $spieler_id)
    {
        $this->id = $spieler_id;
        $this->details = $this->get_details();
    }

    /**
     * Fügt einen neuen Spieler in die Datenbank ein
     *
     * @param string $vorname
     * @param string $nachname
     * @param string $jahrgang
     * @param string $geschlecht
     * @param int $team_id
     * @return bool
     */
    public static function set_new_spieler(string $vorname, string $nachname, string $jahrgang, string $geschlecht,
                                           int $team_id): bool
    {
        $saison = Config::SAISON;
        // Es wird getestet, ob der Spieler bereits existiert:
        $sql = "
                SELECT spieler_id, team_id, letzte_saison 
                FROM spieler 
                WHERE vorname = ? 
                AND nachname = ?
                AND jahrgang = ? 
                AND geschlecht = ?
                ";
        $params = [$vorname, $nachname, $jahrgang, $geschlecht];
        $result = dbi::$db->query($sql, $params)->fetch_row();
        $spieler_id = $result['spieler_id'] ?? 0;

        if ($spieler_id > 0) { // Testen ob der Spieler schon existiert
            if ($result['letzte_saison'] < $saison) { // Testet ob der Spieler aus der Datenbank übernommen werden kann
                $sql = "
                        UPDATE spieler 
                        SET team_id = ?, letzte_saison = ?
                        WHERE spieler_id = ?
                        ";
                $params = [$team_id, $saison, $spieler_id];
                dbi::$db->query($sql, $params)->log();
                Form::info("Der Spieler wurde vom Team " . Team::teamid_to_teamname($result['team_id']) . " übernommen.");
                return true;
            } else {
                Form::error("Der Spieler steht bereits im Kader für folgendes Team: "
                    . Team::teamid_to_teamname($result['team_id']) . "<br> Bitte wende dich an den Ligaausschuss ("
                    . Form::mailto(Env::LAMAIL) . ")", esc:false);
                return false;
            }
        } else {
            // Spieler wird in Spieler-Datenbank eingetragen
            $sql = "
                    INSERT INTO spieler(vorname, nachname, jahrgang, geschlecht, team_id, letzte_saison) 
                    VALUES (?, ?, ?, ?, ?, ?)
                    ";
            $params = [$vorname, $nachname, $jahrgang, $geschlecht, $team_id, Config::SAISON];
            dbi::$db->query($sql, $params)->log();
            return true;
        }
    }

    /**
     * Check, ob der Spieler dem Kader hinzugefügt werden darf
     *
     * @return bool
     */
    public static function check_timing(): bool
    {
        $saison_ende = strtotime(Config::SAISON_ENDE) + 25 * 60 * 60 - 1; // 23:59:59 am Saisonende
        $heute = time();
        if ($saison_ende > $heute) {
            return true;
        }
        return false;
    }

    /**
     * Gibt den Teamkader des Teams mit der entsprechenden TeamID zurück
     *
     * @param int $team_id
     * @param int $saison
     * @return array
     */
    public static function get_teamkader(int $team_id, int $saison = Config::SAISON): array
    {
        $sql = "
                SELECT *  
                FROM spieler 
                WHERE team_id = ? 
                AND letzte_saison = ?
                ORDER BY letzte_saison DESC, vorname
                ";
        return dbi::$db->query($sql, $team_id, $saison)->esc()->fetch('spieler_id');
    }

    /**
     * Alte Teamkadereinträge für die Rückmeldung von Spielern
     *
     * @param int $team_id
     * @return array
     */
    public static function get_teamkader_vorsaison(int $team_id): array
    {
        $kader_vorsaison = self::get_teamkader($team_id, Config::SAISON - 1);
        $kader_vorvorsaison = self::get_teamkader($team_id, Config::SAISON - 2); // Ausnahme für die Saison 20/21, da viele Teams ihre Spieler in der Corona_Saison nicht zurückgemeldet haben
        return $kader_vorsaison + $kader_vorvorsaison;
    }

    /**
     * Anzahl der Spieler in der Datenbank
     *
     * Zählt die Spieler welche in dieser oder in der letzten Saison in einem Kader waren
     *
     * @return int
     */
    public static function get_spieler_anzahl(): int
    {
        $saison = Config::SAISON - 1;
        $sql = "
                SELECT count(*) 
                FROM spieler 
                WHERE letzte_saison >= '$saison'
                ";
        return dbi::$db->query($sql)->fetch_one() ?? 0;
    }

    /**
     * Gibt ein Spielerlisten Array aus aller in der DB hinterlegten Spieler mit [Spieler_id] => Vorname Nachname
     *
     * @return array
     */
    public static function get_spielerliste(): array
    {
        $sql = "
                SELECT vorname,nachname,spieler_id 
                FROM spieler 
                ORDER BY vorname
                ";
        $liste = dbi::$db->query($sql)->esc()->fetch('spieler_id');
        foreach ($liste as $id => $x) {
            $spielerliste[$id] = $x['vorname'] . " " . $x['nachname'];
        }
        return $spielerliste ?? [];
    }

    /**
     * Alle Details eines Spielers werden in einem Array zusammen übergeben
     *
     * @return array
     */
    function get_details(): array
    {
        $sql = "
                SELECT spieler.*, tl.teamname  
                FROM spieler 
                INNER JOIN teams_liga tl on spieler.team_id = tl.team_id
                WHERE spieler_id = $this->id
                ";
        return dbi::$db->query($sql)->esc()->fetch_row();
    }

    /**
     * Ein Spieler Detail verändern: $entry -> Spaltenname in der Datenbank, $value->Wert der in die Datenbank eingetragen werden soll
     *
     * @param string $entry
     * @param mixed $value
     */
    function set_detail(string $entry, mixed $value)
    {
        $spalten_namen = dbi::$db->query("SHOW FIELDS FROM spieler")->list('Field');
        if (!in_array($entry, $spalten_namen)) die("Ungültiger Spaltenname");
        $zeit = ($entry == 'team_id' or $entry == 'letzte_saison') ? '' : ', zeit = zeit';
        $entry = "`" . $entry . "`";
        $sql = "
                UPDATE spieler 
                SET $entry = ?
                $zeit 
                WHERE spieler_id = $this->id
                ";
        dbi::$db->query($sql, $value)->log();
    }


    /**
     * Der Spieler wird aus der Datenbank gelöscht
     */
    function delete_spieler()
    {
        $sql = "
                DELETE FROM spieler 
                WHERE spieler_id = $this->id
                ";
        dbi::$db->query($sql)->log();
    }

    /**
     * Anzahl der gültigen Schiedsrichter in aktiven Teams
     *
     * @return int
     */
    public static function get_schiris_anzahl(): int
    {
        $saison = Config::SAISON;
        $sql = "
                SELECT count(*) 
                FROM `spieler` 
                INNER JOIN teams_liga 
                ON teams_liga.team_id = spieler.team_id 
                WHERE teams_liga.aktiv = 'Ja' 
                AND spieler.schiri >= $saison 
                OR spieler.schiri = 'Ausbilder/in'
                ";
        return dbi::$db->query($sql)->esc()->fetch_one() ?? 0;
    }
}