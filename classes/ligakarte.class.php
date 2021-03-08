<?php

/**
 * Class LigaKarte
 * Anzeigen der Google Maps Ligakarte
 */
class LigaKarte
{

    /**
     * Trägt ein Mitspielergesuch in die Datenbank ein
     *
     * @param int $plz
     * @param string $ort
     * @param float $LAT
     * @param float $Lon
     * @param string $name
     * @param string $kontakt
     */
    public static function gesuch_eintragen_db(int $plz, string $ort, float $LAT, float $Lon, string $name, string $kontakt)
    {
        $sql = "
                INSERT INTO ligakarte_gesuch (plz, ort, LAT, Lon, r_name, kontakt) 
                VALUES (?, ?, ?, ?, ?, ?)
                ";
        $params = [$plz, $ort, $LAT, $Lon, $name, $kontakt];
        return db::$db->query($sql, $params)->log();
    }

    /**
     * Gibt ein Array aller Mitspielergesuche zurück
     * @return array
     */
    public static function get_all_gesuche(): array
    {
        $sql = "
                SELECT * 
                FROM ligakarte_gesuch
                WHERE DATE(zeit) >  CURDATE() - INTERVAL 365 DAY
                ";
        return db::$db->query($sql)->esc()->fetch();
    }

    /**
     * Prüft, ob schon ein Mitspielergesuch für die eingebene Postleitzahl existiert.
     *
     * @param int $plz
     * @return bool
     */
    public static function check_gesuch_for_plz_exists(int $plz): bool
    {
        $sql = "
                SELECT * 
                FROM ligakarte_gesuch 
                WHERE PLZ = ?
                ";
        return db::$db->query($sql, $plz)->num_rows() > 0;
    }

    /**
     * Gibt ein Array aller Teamdaten und Teamkoordinaten LAT und Lon heraus
     *
     * @return array
     */
    public static function get_all_team_koordinaten(): array
    {
        $sql = "
                SELECT teams_details.*, plz.*, teams_liga.teamname 
                FROM teams_details 
                INNER JOIN plz 
                ON plz.PLZ = teams_details.plz
                INNER JOIN teams_liga 
                ON teams_details.team_id  = teams_liga.team_id
                WHERE teams_liga.aktiv = 'Ja'
                ORDER BY teams_liga.teamname
                ";
        return db::$db->query($sql)->esc()->fetch();
    }

    /**
     * Wandelt eine PLZ in Longitude ('Lon') und Latitude('LAT') um
     *
     * @param int $plz
     * @return array
     */
    public static function plz_to_lonlat(int $plz): array
    {
        $sql = "
                SELECT Lon,LAT 
                FROM plz 
                WHERE PLZ = ?
                ";
        return db::$db->query($sql, $plz)->esc()->fetch_row();
    }
}