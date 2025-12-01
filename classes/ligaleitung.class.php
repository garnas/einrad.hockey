<?php

/**
 * Class Ligaleitung
 */
class LigaLeitung
{
    /**
     * Gibt ein Array der Ligaleitung aus
     *
     * @param string $funktion
     * @return array
     */
    public static function get_all(string $funktion): array
    {
        $sql = "
                SELECT ligaleitung.*, spieler.vorname, spieler.nachname, teams_liga.teamname
                FROM ligaleitung
                INNER JOIN spieler on ligaleitung.spieler_id = spieler.spieler_id 
                LEFT JOIN teams_liga on spieler.team_id = teams_liga.team_id
                WHERE funktion = ?
                ORDER BY spieler.vorname
                ";
        return db::$db->query($sql, $funktion)->esc()->fetch('spieler_id');
    }

    /**
     * Gibt die Details eines Loginnamens aus
     *
     * @param string $login
     * @return array
     */
    public static function get_details(string $login): array
    {
        $sql = "
                SELECT ligaleitung.*, spieler.vorname, spieler.nachname, teams_liga.teamname
                FROM ligaleitung
                INNER JOIN spieler on ligaleitung.spieler_id = spieler.spieler_id 
                LEFT JOIN teams_liga on spieler.team_id = teams_liga.team_id
                WHERE login = ?
                ";
        return db::$db->query($sql, $login)->esc()->fetch_row();
    }

    /**
     * Erneuert das Passwort einer Ligaleitung
     *
     * @param string $login
     * @param string $passwort
     * @param string $passwort_alt
     * @return bool
     */
    public static function set_passwort(string $login, string $passwort, string $passwort_alt): bool
    {
        $details = self::get_details($login);
        // Überprüfung des PWs
        if (!password_verify($passwort_alt, $details['passwort'])) {
            return false;
        }

        // Passwort Hashen
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
        if (!is_string($passwort_hash)) {
            trigger_error("set_passwort fehlgeschlagen.", E_USER_ERROR);
        }

        // Neues Passwort in die Datenbank schreiben
        $sql = "
                UPDATE ligaleitung 
                SET passwort = ?
                WHERE login = ?
                ";
        db::$db->query($sql, $passwort_hash, $login)->log();
        $_SESSION['logins']['la']['passwort'] = $passwort_hash;
        return true;
    }

    /**
     * Login der Ligaleitung
     *
     * @param string $login
     * @param string $passwort
     * @param string $funktion 'ligaausschuss', 'schiriausschuss', 'team_social_media', 'technikausschuss', 'ausbilder
     * @return bool
     */
    public static function login(string $login, string $passwort, string $funktion): bool
    {
        $details = self::get_details($login);

        // Existenz prüfen
        if (empty($details)) {
            Html::error("Unbekannter Loginname");
            Helper::log(Config::LOG_LOGIN, "Falscher Login    | Loginname: " . $login);
            return false;
        }

        // Funktion prüfen
        if (self::get_context($funktion) !== self::get_context($details['funktion'])) {
            return false;
        }

        $context = self::get_context($funktion);

        // Passwort prüfen
        if (password_verify($passwort, $details['passwort'])) {
            $_SESSION['logins'][$context]['id'] = $details['ligaleitung_id'];
            $_SESSION['logins'][$context]['login'] = $details['login'];
            Helper::log(
                Config::LOG_LOGIN, "Erfolgreich       | Loginname: " . $login . " | Kontext: " . $context
            );
            return true;
        }

        // Passwort falsch
        Helper::log(Config::LOG_LOGIN, "Falsches Passwort | Loginname: " . $login);
        Html::error("Falsches Passwort");
        return false;
    }

    public static function is_logged_in(string $funktion = ""): bool
    {
        $context = self::get_context($funktion);
        return isset($_SESSION['logins'][$context]);
    }

    private static function get_context(string $funktion): string
    {
        return match($funktion) {
            "admin", "ligaausschuss" => "la",
            "team_social_media" => "oa",
        };
    }

}
