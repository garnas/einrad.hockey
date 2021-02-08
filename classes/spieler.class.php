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
    public int $spieler_id;

    /**
     * Spieler constructor.
     * @param $spieler_id
     */
    function __construct($spieler_id)
    {
        $this->spieler_id = $spieler_id;
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
    public static function create_new_spieler(string $vorname, string $nachname, string $jahrgang, string $geschlecht,
                                              int $team_id): bool
    {
        $saison = Config::SAISON;
        //Es wird getestet, ob der Spieler bereits existiert:
        $sql = "
                SELECT spieler_id, team_id, letzte_saison 
                FROM spieler 
                WHERE vorname='$vorname' 
                AND nachname='$nachname' 
                AND jahrgang='$jahrgang' 
                AND geschlecht='$geschlecht'
                ";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        $spieler_id = $result['spieler_id'] ?? 0;

        if ($spieler_id > 0) { // Testen ob der Spieler schon existiert
            if ($result['letzte_saison'] < $saison) { // Testet ob der Spieler aus der Datenbank übernommen werden kann
                $sql = "
                        UPDATE spieler 
                        SET team_id = '$team_id', letzte_saison = ' $saison' 
                        WHERE spieler_id = '$spieler_id'
                        ";
                db::writedb($sql);
                Form::affirm("Der Spieler wurde vom Team " . Team::teamid_to_teamname($result['team_id']) . " übernommen.");
                return true;
            } else {
                Form::error("Der Spieler steht bereits im Kader für folgendes Team: " . Team::teamid_to_teamname($result['team_id']) . "<br> Bitte wende dich an den Ligaausschuss (" . Form::mailto(Config::LAMAIL) . ")");
                return false;
            }
        } else {
            // Spieler wird in Spieler-Datenbank eingetragen
            $sql = "
                    INSERT INTO spieler(vorname, nachname, jahrgang, geschlecht, team_id, letzte_saison) 
                    VALUES ('$vorname','$nachname','$jahrgang','$geschlecht','$team_id','" . Config::SAISON . "')
                    ";
            db::writedb($sql);
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
        $heute = Config::time_offset();
        if ($saison_ende > $heute) {
            return true;
        }
        return false;
    }

    /**
     * Gibt den Teamkader des Teams mit der entsprechenden TeamID zurück
     *
     * @param $team_id
     * @param int $saison
     * @return array
     */
    public static function get_teamkader($team_id, $saison = Config::SAISON): array
    {
        $sql = "
                SELECT *  
                FROM spieler 
                WHERE team_id='$team_id' 
                AND letzte_saison = '$saison'
                ORDER BY letzte_saison DESC, vorname
                ";
        $result = db::readdb($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            if (strtotime($x['zeit']) < 0) {
                $x['zeit'] = '--'; // Diese Funktion wurde erst am später hinzugefügt.
            }
            $return[$x['spieler_id']] = $x;

        }
        return db::escape($return ?? []); // Array
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
        $return = $kader_vorsaison + $kader_vorvorsaison;
        return db::escape($return ?? []);
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
        $result = db::readdb($sql);
        $return = mysqli_fetch_assoc($result);
        return db::escape($return['count(*)']);
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
        $result = db::readdb($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            $spielerliste[$x['spieler_id']] = $x['vorname'] . " " . $x['nachname'];
        }
        return db::escape($spielerliste ?? []);
    }

    /**
     * Alle Details eines Spielers werden in einem Array zusammen übergeben
     *
     * @return array
     */
    function get_spieler_details(): array
    {
        $spieler_id = $this->spieler_id;
        $sql = "
                SELECT *  
                FROM spieler 
                WHERE spieler_id = '$spieler_id'
                ";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        return db::escape($result);
    }

    /**
     * Ein Spieler Detail verändern: $entry -> Spaltenname in der Datenbank, $value->Wert der in die Datenbank eingetragen werden soll
     *
     * @param string $entry
     * @param mixed $value
     */
    function set_spieler_detail(string $entry, mixed $value)
    {
        $spieler_id = $this->spieler_id;
        if ($entry == 'team_id' or $entry == 'letzte_saison') {
            $zeit = '';
        } else {
            $zeit = ', zeit=zeit';
        }
        $sql = "
                UPDATE spieler 
                SET $entry = '$value'$zeit 
                WHERE spieler_id='$spieler_id'
                ";
        db::writedb($sql);
    }


    /**
     * Der Spieler wird aus der Datenbank gelöscht
     */
    function delete_spieler()
    {
        $spieler_id = $this->spieler_id;
        $sql = "
                DELETE FROM spieler 
                WHERE spieler_id='$spieler_id'
                ";
        db::writedb($sql);
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
                AND spieler.schiri >= '$saison' 
                OR spieler.schiri = 'Ausbilder/in'
                ";
        return mysqli_fetch_assoc(db::readdb($sql))['count(*)'];
    }
}