<?php

class Stats
{
    /**
     * Anzahl der Spieler in der Datenbank
     *
     * Zählt die Spieler welche in dieser oder in der letzten Saison in einem Kader waren
     *
     * @return int
     */
    public static function get_anzahl(): int
    {
        $sql = "
                SELECT count(*) 
                FROM spieler 
                WHERE letzte_saison >= ?
                AND team_id IS NOT NULL
                ";
        return db::$db->query($sql, Config::SAISON - 1 )->fetch_one() ?? 0;
    }

    /**
     * Anzahl der gültigen Schiedsrichter in aktiven Teams
     *
     * @return int
     */
    public static function get_schiris_anzahl(): int
    {
        $sql = "
                SELECT count(*) 
                FROM `spieler` 
                INNER JOIN teams_liga 
                ON teams_liga.team_id = spieler.team_id 
                WHERE teams_liga.aktiv = 'Ja' 
                AND spieler.schiri >= ?
                ";
        return db::$db->query($sql, Config::SAISON)->esc()->fetch_one() ?? 0;
    }
}