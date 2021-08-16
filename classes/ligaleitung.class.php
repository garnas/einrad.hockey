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
            Html::error("Falsches Passwort");
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
     * @param string $funktion 'ligaausschuss', 'schiriausschuss', 'oeffentlichkeitsausschuss', 'technikausschuss', 'ausbilder
     * @return bool
     */
    public static function login(string $login, string $passwort, string $funktion): bool
    {
        $details = self::get_details($login);

        // Existenz prüfen
        if (empty($details)) {
            Html::error("Unbekannter Loginname");
            Helper::log(Config::LOG_LOGIN, "Falscher LC-Login | Loginname: " . $login);
            return false;
        }

        // Funktion prüfen
        if ($funktion !== $details['funktion']) {
            Html::error("Fehlende Berichtigung");
            return false;
        }

        // Passwort prüfen
        if (password_verify($passwort, $details['passwort'])) {
            $_SESSION['logins']['la']['id'] = $details['ligaleitung_id'];
            $_SESSION['logins']['la']['login'] = $details['login'];
            Helper::log(Config::LOG_LOGIN, "Erfolgreich       | Loginname: " . $login);
            return true;
        }

        // Passwort falsch
        Helper::log(Config::LOG_LOGIN, "Falsches Passwort | Loginname: " . $login);
        Html::error("Falsches Passwort");
        return false;
    }
}
