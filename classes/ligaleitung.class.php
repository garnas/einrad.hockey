<?php

/**
 * Class Ligaleitung
 */
class Ligaleitung
{
    /**
     * Gibt die in der DB gespeicherte Ligaleitung aus
     *
     * @return array
     */
    public static function get_la(): array
    {
        $sql = "
                SELECT r_name, team_id, email 
                FROM ausschuss_liga 
                ORDER BY r_name
                ";
        return dbi::$db->query($sql)->esc()->fetch();
    }

    /**
     * Get Technikausschuss
     *
     * @return array
     */
    public static function get_tk(): array
    {
        $sql = "
                SELECT r_name, team_id 
                FROM ausschuss_technik
                ORDER BY r_name
                ";
        return dbi::$db->query($sql)->esc()->fetch();
    }

    /**
     * Get Schiriausschuss
     * @return array
     */
    public static function get_sa(): array
    {
        $sql = "
                SELECT r_name, team_id 
                FROM ausschuss_schiri
                ORDER BY r_name
                ";
        return dbi::$db->query($sql)->esc()->fetch();
    }

    /**
     * Get Öffentlichkeitsausschuss
     *
     * @return array
     */
    public static function get_oa(): array
    {
        $sql = "
                SELECT r_name, team_id 
                FROM ausschuss_oeffi
                ORDER BY r_name
                ";
        return dbi::$db->query($sql)->esc()->fetch();
    }

    /**
     * Get Schiriausbilder
     *
     * @return array
     */
    public static function get_ausbilder(): array
    {
        $sql = "
                SELECT vorname, nachname, team_id 
                FROM spieler 
                WHERE schiri = 'Ausbilder/in'
                ORDER BY vorname
                ";
        return dbi::$db->query($sql)->esc()->fetch();
    }

    /**
     * Gibt die Ligaausschuss-ID eines Loginnamens aus
     *
     * @param string $name
     * @return int
     */
    public static function get_la_id(string $name): int
    {
        $sql = "
                SELECT ligaausschuss_id 
                FROM ausschuss_liga 
                WHERE login_name = ?
                ";
        return dbi::$db->query($sql, $name)->esc()->fetch_one();
    }

    /**
     * Gibt den Passwort-Hash einer La-ID aus
     *
     * @param int $la_id
     * @return string
     */
    public static function get_la_password(int $la_id): string
    {
        $sql = "
                SELECT passwort
                FROM ausschuss_liga 
                WHERE ligaausschuss_id = ?
                ";
        return dbi::$db->query($sql, $la_id)->esc()->fetch_one();
    }

    /**
     * Gibt den realen Namen einer La-ID aus
     * @param int $la_id
     * @return string
     */
    public static function get_la_name(int $la_id): string
    {
        $sql = "
                SELECT r_name  
                FROM ausschuss_liga 
                WHERE ligaausschuss_id = ?
                ";
        return dbi::$db->query($sql, $la_id)->esc()->fetch_one();
    }

    /**
     * Setzt das Passwort der La-ID
     *
     * @param int $la_id
     * @param string $passwort
     */
    public static function set_la_password(int $la_id, string $passwort)
    {
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
        $sql = "
                UPDATE ausschuss_liga 
                SET passwort = ? 
                WHERE  ligaausschuss_id = ?
                ";
        dbi::$db->query($sql, $passwort_hash, $la_id)->log();
    }
}

