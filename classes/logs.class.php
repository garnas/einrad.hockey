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
}
