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
    public static function get_spieler_anzahl(int $saison = Config::SAISON): int
    {
        $sql = "
                SELECT count(*) 
                FROM spieler 
                WHERE letzte_saison >= ?
                AND team_id IS NOT NULL
                ";
        return db::$db->query($sql, $saison - 1 )->fetch_one() ?? 0;
    }

    /**
     * Anzahl der gültigen Schiedsrichter in aktiven Teams
     *
     * @return int
     */
    public static function get_schiris_anzahl(int $saison = Config::SAISON): int
    {
        $sql = "
                SELECT count(*) 
                FROM `spieler` 
                INNER JOIN teams_liga 
                ON teams_liga.team_id = spieler.team_id 
                WHERE teams_liga.aktiv = 'Ja' 
                AND spieler.schiri >= ?
                ";
        return db::$db->query($sql, $saison)->esc()->fetch_one() ?? 0;
    }
    
    /**
     * Gibt alle gefallenen Tore inklusive Penaltys einer Saison aus.
     *
     * @param int $saison
     * @return int
     */
    public static function get_tore_anzahl(int $saison = Config::SAISON): int
    {
        $sql = "
                SELECT sum(tore_a) + sum(tore_b) + sum(penalty_a) + sum(penalty_b) AS tore
                FROM spiele
                INNER JOIN turniere_liga tl on spiele.turnier_id = tl.turnier_id
                WHERE tl.saison = ?
                ";
        return db::$db->query($sql, $saison)->esc()->fetch_one() ?? 0;
    }

    /**
     * Gespielte Spiele
     *
     * @param int $saison
     * @return int
     */
    public static function get_spiele_anzahl(int $saison = Config::SAISON): int
    {
        $sql = "
                SELECT count(*) AS spiele
                FROM spiele
                INNER JOIN turniere_liga tl on spiele.turnier_id = tl.turnier_id
                WHERE tl.saison = ?
                AND tore_b IS NOT NULL
                AND tore_a IS NOT NULL
                ";
        return db::$db->query($sql, $saison)->esc()->fetch_one() ?? 0;
    }
    
    /**
     * Gespielte Minuten
     *
     * @param int $saison
     * @return int
     */
    public static function get_spielminuten_anzahl(int $saison = Config::SAISON): int
    {
        $sql = "
                SELECT sum(sd.anzahl_halbzeiten * sd.halbzeit_laenge)
                FROM spiele
                INNER JOIN turniere_liga tl on spiele.turnier_id = tl.turnier_id
                INNER JOIN turniere_details td on spiele.turnier_id = td.turnier_id
                INNER JOIN spielplan_details sd on tl.spielplan_vorlage = sd.spielplan
                WHERE tl.saison = ?
                AND tore_b IS NOT NULL
                AND tore_a IS NOT NULL
                ";
        return db::$db->query($sql, $saison)->esc()->fetch_one() ?? 0;
    }

    /**
     * Gespielte Turniere pro Team
     *
     * @param int $saison
     * @return array
     */
    public static function get_turniere_team(int $saison = Config::SAISON): array
    {
        // Findet die drei Teams, welche die meisten Turniere bisher gespielt haben
        // Sortiert nach Zufall bei gleichstand
        $sql = "
                SELECT teams_liga.teamname, count(*) as gespielt 
                FROM turniere_liste 
                INNER JOIN turniere_liga 
                ON turniere_liste.turnier_id = turniere_liga.turnier_id 
                INNER JOIN teams_liga 
                ON teams_liga.team_id = turniere_liste.team_id 
                WHERE teams_liga.aktiv = 'Ja' 
                AND teams_liga.ligateam = 'Ja'
                AND turniere_liga.saison = ? 
                AND turniere_liste.liste = 'spiele' 
                AND turniere_liga.phase = 'ergebnis' 
                GROUP BY teams_liga.teamname 
                ORDER BY gespielt desc, rand()
                LIMIT 3
                ";
        return db::$db->query($sql, $saison)->esc()->fetch();
    }    

    /**
     * Gewonnene Spiele pro Team
     * 
     * @param int $saison
     * @return array
     */
    public static function get_gew_spiele_team(int $saison = Config::SAISON): array
    {
        // Gewonnen Team A
        $sqla = "
                SELECT COUNT(*) AS gew, team_id_a
                FROM spiele
                INNER JOIN turniere_liga 
                ON spiele.turnier_id = turniere_liga.turnier_id 
                INNER JOIN teams_liga
                ON spiele.team_id_a = teams_liga.team_id
                WHERE (tore_a > tore_b OR penalty_a > penalty_b)
                AND turniere_liga.saison = ?
                AND teams_liga.ligateam = 'Ja'
                GROUP BY team_id_a
                ORDER BY gew, RAND()
                ";
        $gew = [];
        foreach (db::$db->query($sqla, $saison)->esc()->fetch() as $x) {
            $gew[$x['team_id_a']] = $x['gew'];
        }
        // Addition der Tore Team B
        $sqlb = "
                SELECT COUNT(*) AS gew, team_id_b
                FROM spiele
                INNER JOIN turniere_liga
                ON spiele.turnier_id = turniere_liga.turnier_id
                INNER JOIN teams_liga
                ON spiele.team_id_b = teams_liga.team_id
                WHERE (tore_a < tore_b OR penalty_a < penalty_b)
                AND turniere_liga.saison = ?
                AND teams_liga.ligateam = 'Ja'
                GROUP BY team_id_b
                ORDER BY RAND()
                ";
        foreach (db::$db->query($sqlb, $saison)->esc()->fetch() as $x) {
            if (isset($gew[$x['team_id_b']])) {
                $gew[$x['team_id_b']] += $x['gew'];
            } else {
                $gew[$x['team_id_b']] = $x['gew'];
            }
        }
        arsort($gew);
        return array_slice($gew, 0, 3, true);
    }
}