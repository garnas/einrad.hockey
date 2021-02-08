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
                VALUES ('$plz','$ort','$LAT','$Lon','$name','$kontakt')
                ";
        db::writedb($sql);
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
                ";
        $result = db::readdb($sql);
        while ($x = mysqli_fetch_assoc($result)) {
            if ((Config::time_offset() - strtotime($x['zeit'])) < 365 * 24 * 60 * 60) {
                $return[] = $x;
            }
        }
        return db::escape($return ?? []);
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
                WHERE PLZ = '$plz'
                ";
        $result = db::readdb($sql);
        $result = mysqli_fetch_assoc($result);
        if (!empty($result)){
            return true;
        }
        return false;
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
        $result = db::readdb($sql);
        while ($team = mysqli_fetch_assoc($result)) {
           $return[] = $team;
        }
        return db::escape($return ?? []);
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
                WHERE PLZ = '$plz'
                ";
        $result = db::readdb($sql);
        $return = mysqli_fetch_assoc($result);
        return db::escape($return);
    }
}