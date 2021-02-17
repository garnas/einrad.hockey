<?php

/**
 * Class Ligaleitung
 */
class Ligaleitung
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
                SELECT *, spieler.vorname, spieler.nachname, teams_liga.teamname
                FROM ligaleitung
                INNER JOIN spieler on ligaleitung.spieler_id = spieler.spieler_id 
                LEFT JOIN teams_liga on spieler.team_id = teams_liga.team_id
                WHERE funktion = ?
                ORDER BY spieler.vorname
                ";
        return dbi::$db->query($sql, $funktion)->esc()->fetch('spieler_id');
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
        return dbi::$db->query($sql, $login)->esc()->fetch_row();
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
            Form::error("Falsches Passwort");
            return false;
        }

        // Passwort Hashen
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
        if (!is_string($passwort_hash)) {
            die("Ein Fehler ist aufgetreten");
        }

        // Neues Passwort in die Datenbank schreiben
        $sql = "
                UPDATE ligaleitung 
                SET passwort = ?
                WHERE login = ?
                ";
        dbi::$db->query($sql, $passwort_hash, $login)->log();
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
            Form::error("Unbekannter Loginname");
            Form::log(Config::LOG_LOGIN, "Falscher LC-Login | Loginname: " . $login);
            return false;
        }

        // Funktion prüfen
        if ($funktion !== $details['funktion']) {
            Form::error("Fehlende Berichtigung");
            return false;
        }

        // Passwort prüfen
        if (password_verify($passwort, $details['passwort'])) {
            $_SESSION['logins']['la']['id'] = $details['ligaleitung_id'];
            $_SESSION['logins']['la']['login'] = $details['login'];
            Form::log(Config::LOG_LOGIN, "Erfolgreich       | Loginname: " . $login);
            return true;
        }

        // Passwort falsch
        Form::log(Config::LOG_LOGIN, "Falsches Passwort | Loginname: " . $login);
        Form::error("Falsches Passwort");
        return false;
    }

    static function umzug()
    {
        $sql = "
                SELECT * 
                FROM ausschuss_liga";
        $data = dbi::$db->query($sql)->fetch();

        foreach ($data as $x) {
            $sql = "INSERT INTO ligaleitung (spieler_id, funktion, login, passwort, email)
                   Values(?,?,?,?,?)";
            dbi::$db->query($sql, $i++, 'ligaausschuss', $x['login_name'], $x['passwort'], $x['email'])->log();
        }
    }

    static function umzug2($table, $funktion)
    {
        $sql = "
                SELECT * 
                FROM $table
                ";
        $la = dbi::$db->query($sql)->fetch();

        $sql = "SELECT * FROM spieler";
        $spieler = dbi::$db->query($sql)->fetch();
        $i = 0;
        foreach ($spieler as $s) {
            foreach ($la as $l) {
                if (str_contains($l['r_name'], $s['vorname']) && str_contains($l['r_name'], $s['nachname'])) {
                    if ($funktion === "ligaausschuss") {
                        $sql = "INSERT INTO ligaleitung (spieler_id, funktion, login, passwort, email)
                            Values(?,?,?,?,?)";
                        dbi::$db->query($sql, $s['spieler_id'], $funktion, $s['vorname'], $l['passwort'], $l['email'])->log();
                        $i++;
                    } else {
                        $sql = "INSERT INTO ligaleitung (spieler_id, funktion)
                            Values(?,?)";
                        dbi::$db->query($sql, $s['spieler_id'], $funktion)->log();
                        $i++;
                    }
                }
            }
        }
        dbi::debug([$i, $table, $funktion]);
    }
    static function umzug3()
    {
        $sql = "SELECT * FROM spieler";
        $spieler = dbi::$db->query($sql)->fetch();
        $i = 0;
        foreach ($spieler as $s) {
            if ($s['schiri'] === "Ausbilder/in") {
                $sql = "INSERT INTO ligaleitung (spieler_id, funktion)
                            Values(?,?)";
                dbi::$db->query($sql, $s['spieler_id'], 'schiriausbilder')->log();
                $i++;
            }
        }
        dbi::debug([$i, 'ausbilder']);
    }
}

