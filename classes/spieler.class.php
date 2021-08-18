<?php

/**
 * Class Spieler
 */
class Spieler
{
    public int $id;
    public int $spieler_id;
    public int $team_id;
    public ?string $vorname;
    public ?string $nachname;
    public ?string $jahrgang;
    public ?string $geschlecht;
    public ?string $schiri;
    public ?string $junior;
    public ?string $zeit;


    /**
     * Spieler constructor.
     */
    public function __construct()
    {
        db::debug("Alte Spielerklasse");
    }

    public static function get(int $id): object|null
    {
        $sql = "
                SELECT * 
                FROM spieler 
                WHERE spieler_id = ?
                ";
        return db::$db->query($sql, $id)->fetch_object('Spieler');
    }

    public static function exists(int $id): bool
    {
        $sql = "
                SELECT * 
                FROM spieler 
                WHERE spieler_id = ?
                ";
        return db::$db->query($sql, $id)->affected_rows() > 0;
    }

    public static function get_all(): array
    {
        $sql = "
                SELECT * 
                FROM spieler 
                ";
        return db::$db->query($sql)->fetch_objects('Spieler', key: 'spieler_id');
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
        $saison_ende = strtotime(Config::SAISON_ENDE) + 24 * 60 * 60 - 1; // 23:59:59 am Saisonende
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
                SELECT spieler.*, l.funktion  
                FROM spieler
                LEFT JOIN ligaleitung l on spieler.spieler_id = l.spieler_id
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
        $zeit = ($entry === 'team_id' || $entry === 'letzte_saison') ? 'zeit = CURRENT_TIMESTAMP' : ', zeit = zeit';
        $entry = "`" . $entry . "`";
        $sql = "
                UPDATE spieler 
                SET $entry = ?
                $zeit 
                WHERE spieler_id = $this->id
                ";
        db::$db->query($sql, $value)->log();
    }
    public function get_ausbilder(): array {
        $sql = "SELECT funktion FROM ligaleitung WHERE spieler_id = $this->spieler_id";
        return db::$db->query($sql)->fetch();
    }
}