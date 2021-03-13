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
    public function __construct(int $spieler_id)
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
        $result = db::$db->query($sql, $params)->fetch_row();
        $spieler_id = $result['spieler_id'] ?? 0;

        if ($spieler_id > 0) { // Testen ob der Spieler schon existiert
            if ($result['letzte_saison'] < Config::SAISON) { // Testet ob der Spieler aus der Datenbank übernommen werden kann
                $sql = "
                        UPDATE spieler 
                        SET team_id = ?, letzte_saison = ?
                        WHERE spieler_id = ?
                        ";
                $params = [$team_id, Config::SAISON, $spieler_id];
                db::$db->query($sql, $params)->log();
                Html::info("Der Spieler wurde vom Team " . Team::id_to_name($result['team_id']) . " übernommen.");
                return true;
            }

            Html::error("Der Spieler steht bereits im Kader für folgendes Team: "
                . Team::id_to_name($result['team_id']) . "<br> Bitte wende dich an den Ligaausschuss ("
                . Html::mailto(Env::LAMAIL) . ")", esc:false);
            return false;
        }

        // Spieler wird in Spieler-Datenbank eingetragen
        $sql = "
                INSERT INTO spieler(vorname, nachname, jahrgang, geschlecht, team_id, letzte_saison) 
                VALUES (?, ?, ?, ?, ?, ?)
                ";
        $params = [$vorname, $nachname, $jahrgang, $geschlecht, $team_id, Config::SAISON];
        db::$db->query($sql, $params)->log();
        return true;
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
        return $saison_ende > $heute;
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
        return db::$db->query($sql, $team_id, $saison)->esc()->fetch('spieler_id');
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
        $kader_vorvorsaison = self::get_teamkader($team_id, Config::SAISON - 2);
            // Ausnahme für die Saison 20/21, da Teams ihre Spieler wegen Corona nicht zurückgemeldet wurden
        return $kader_vorsaison + $kader_vorvorsaison;
    }

    /**
     * Anzahl der Spieler in der Datenbank
     *
     * Zählt die Spieler welche in dieser oder in der letzten Saison in einem Kader waren
     *
     * @return int
     */
    public static function get_anzahl(): int
    {
        $saison = Config::SAISON - 1;
        $sql = "
                SELECT count(*) 
                FROM spieler 
                WHERE letzte_saison >= '$saison'
                AND team_id IS NOT NULL
                ";
        return db::$db->query($sql)->fetch_one() ?? 0;
    }

    /**
     * Gibt ein Spielerlisten Array aus aller in der DB hinterlegten Spieler mit [Spieler_id] => Vorname Nachname
     *
     * @return array
     */
    public static function get_spielerliste(): array
    {
        $sql = "
                SELECT vorname, nachname, spieler_id 
                FROM spieler 
                ORDER BY vorname
                ";
        $liste = db::$db->query($sql)->esc()->fetch('spieler_id');
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
    public function get_details(): array
    {
        $sql = "
                SELECT spieler.*, tl.teamname  
                FROM spieler 
                LEFT JOIN teams_liga tl on spieler.team_id = tl.team_id
                WHERE spieler_id = $this->id
                ";
        return db::$db->query($sql)->esc()->fetch_row();
    }

    /**
     * Ein Spieler Detail verändern: $entry -> Spaltenname in der Datenbank, $value->Wert der in die Datenbank eingetragen werden soll
     *
     * @param string $entry
     * @param mixed $value
     */
    public function set_detail(string $entry, mixed $value): void
    {
        $spalten_namen = db::$db->query("SHOW FIELDS FROM spieler")->list('Field');
        if (!in_array($entry, $spalten_namen, true)) {
            trigger_error("Ungültiger Spaltenname", E_USER_ERROR);
        }
        $zeit = ($entry === 'team_id' || $entry === 'letzte_saison') ? '' : ', zeit = zeit';
        $entry = "`" . $entry . "`";
        $sql = "
                UPDATE spieler 
                SET $entry = ?
                $zeit 
                WHERE spieler_id = $this->id
                ";
        db::$db->query($sql, $value)->log();
    }


    /**
     * Der Spieler wird aus der Datenbank gelöscht
     */
    public function delete_spieler(): void
    {
        $sql = "
                DELETE FROM spieler 
                WHERE spieler_id = $this->id
                ";
        db::$db->query($sql)->log();
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
        return db::$db->query($sql)->esc()->fetch_one() ?? 0;
    }
}