<?php

class Logs
{
    /**
     * Get Logs
     *
     * @return array
     */
    public static function get_turnier_logs(): array
    {
        $sql = "
            SELECT turniere_liga.datum, turniere_details.ort, turniere_liga.tblock, turniere_log.turnier_id, log_text, autor, zeit
            FROM turniere_log
            LEFT JOIN turniere_liga ON turniere_log.turnier_id = turniere_liga.turnier_id
            LEFT JOIN turniere_details ON turniere_log.turnier_id = turniere_details.turnier_id
            ORDER BY turniere_log.zeit DESC
            ";
        return db::$db->query($sql)->esc()->fetch();
    }

    /**
     * Get Spielerausleihe
     * 
     * @return array
     */
    public static function get_spielerausleihe(int $saison = Config::SAISON)
    {
        $sql = "
            SELECT spieler_ausleihen.turnier_id, turniere_liga.datum, turniere_details.ort, spieler_ausleihen.spieler, spieler_ausleihen.team_auf, spieler_ausleihen.team_ab
            FROM spieler_ausleihen
            LEFT JOIN turniere_liga ON turniere_liga.turnier_id = spieler_ausleihen.turnier_id
            LEFT JOIN turniere_details ON turniere_details.turnier_id = spieler_ausleihen.turnier_id
            WHERE saison = ?
        ";
        return db::$db->query($sql, $saison)->esc()->fetch();
    }
}
