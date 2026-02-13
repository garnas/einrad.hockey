<?php

class Stats
{
    public static function persist_spieler_statistik(): void
    {
        $sql = "
            INSERT INTO `spieler_statistik` (`id`, `date`, `saison`, `geschlecht`, `anzahl`)
            SELECT NULL, current_timestamp() as date, max(letzte_saison) as saison, geschlecht, count(*) as anzahl
            FROM spieler
            WHERE letzte_saison >= ?
            AND team_id IS NOT NULL
            GROUP BY geschlecht;
        ";

        db::$db->query($sql, Config::SAISON)->log();
    }
    /**
     * Anzahl der Spieler zu unterschiedlichen Stichtagen im aktuellen Jahr
     *
     *
     * @param int|null $time
     * @return array
     */
    public static function get_aktuelle_spieler_anzahl(?int $time = null): array
    {
        // $time parameter for unit testing
        if ($time === null) {
            $time = time();
        }
        $year = date('Y', $time);
        $cutoff = [ 
            "$year-06-30 23:59:59", 
            "$year-01-31 23:59:59", 
            ($year - 1) . "-06-30 23:59:59",
        ];

        foreach ($cutoff as $c) {
            if (($cs = strtotime($c)) <= $time) {
                $lastCutoff = $cs;
                break; 
            }
        }
    
        $sql = "
                SELECT SUM(anzahl) 
                FROM spieler_statistik
                WHERE date = (
                    SELECT max(date) 
                    FROM spieler_statistik 
                    WHERE date <= ?
                );
                ";
        
        $number = db::$db->query($sql, date('Y-m-d H:i:s', $lastCutoff))->fetch_one() ?? 0;
        return ['cutoff' => date('d.m.Y', $lastCutoff), 'number' => $number];
    }


    /**
     * Anzahl der gültigen Schiedsrichter in aktiven Teams
     *
     * @param int $saison
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
            FROM spiele s
            INNER JOIN turniere_liga t
                ON s.turnier_id = t.turnier_id
            WHERE (t.art LIKE 'I' OR t.art LIKE 'II' OR t.art LIKE 'III')
            AND s.tore_a IS NOT NULL
            AND s.tore_b IS NOT NULL
            AND t.canceled = 0
            AND t.saison =	?
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
            SELECT sum(sd.anzahl_halbzeiten * sd.halbzeit_laenge) as minuten
            FROM spiele s
            INNER JOIN turniere_liga tl on s.turnier_id = tl.turnier_id
            INNER JOIN turniere_details td on s.turnier_id = td.turnier_id
            INNER JOIN spielplan_details sd on tl.spielplan_vorlage = sd.spielplan
            WHERE tl.art IN ('I', 'II', 'III')
            AND s.tore_a IS NOT NULL
            AND s.tore_b IS NOT NULL
            AND tl.canceled = 0
            AND tl.saison =	?
        ";
        return db::$db->query($sql, $saison)->esc()->fetch_one() ?? 0;
    }

    /**
     * Gespielte Turniere pro Team
     *
     * @param int $saison
     * @param int $limit
     * @return array
     */
    public static function get_turniere_team(int $saison = Config::SAISON, int $limit = 999): array
    {
        $sql = "
            SELECT teams_liga.teamname, count(*) as gespielt 
            FROM turniere_liste 
            INNER JOIN turniere_liga 
                ON turniere_liste.turnier_id = turniere_liga.turnier_id 
            INNER JOIN teams_liga 
                ON teams_liga.team_id = turniere_liste.team_id
            WHERE turniere_liga.saison = ?
            AND turniere_liga.phase = 'ergebnis'
            AND turniere_liga.art IN ('I', 'II', 'III')
            AND turniere_liste.liste = 'setzliste'
            AND teams_liga.aktiv = 'Ja' 
            AND teams_liga.ligateam = 'Ja'
            GROUP BY teams_liga.teamname 
            ORDER BY gespielt desc, rand()
            LIMIT ?
        ";
        
        $teams = db::$db->query($sql, $saison, $limit)->esc()->fetch();
        
        $rang = 1;
        $vorher_rang = 1;
        $vorher_anz = 0;
        foreach ($teams as $team_id => $team) {
            if ($teams[$team_id]['gespielt'] >= $vorher_anz) {
                $teams[$team_id]['platz'] = $vorher_rang;
            } else {
                $teams[$team_id]['platz'] = $rang;
            }

            $vorher_anz = $teams[$team_id]['gespielt'];
            $vorher_rang = $teams[$team_id]['platz'];
            $rang++;
        }

        return $teams;
    }    

    /**
     * Gewonnene Spiele pro Team
     * 
     * @param int $saison
     * @param int $limit
     * @return array
     */
    public static function get_gew_spiele_team(int $saison = Config::SAISON, int $limit = 999): array
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
                AND turniere_liga.art IN ('I', 'II', 'III')
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
                AND turniere_liga.art IN ('I', 'II', 'III')
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

        $teams = [];
        $rang = 1;
        $vorher_rang = 1;
        $vorher_anz = 0;
        foreach ($gew as $team_id => $siege) {
            $teams[$team_id]['teamname'] = Team::id_to_name($team_id);
            $teams[$team_id]['siege'] = $siege;

            if ($teams[$team_id]['siege'] >= $vorher_anz) {
                $teams[$team_id]['platz'] = $vorher_rang;
            } else {
                $teams[$team_id]['platz'] = $rang;
            }

            $vorher_anz = $teams[$team_id]['siege'];
            $vorher_rang = $teams[$team_id]['platz'];
            $rang++;
        }

        return array_slice($teams, 0, $limit, true);
    }

    /**
     * Erfolgreich ausgerichtete Turniere pro Team
     *
     * @param int $saison
     * @param int $limit
     * @return array
     */
    public static function get_max_ausrichter(int $saison = Config::SAISON, int $limit = 999): array
    {
        $sql = "
            SELECT teamname, COUNT(*) as anzahl
            FROM turniere_liga
            LEFT JOIN teams_liga ON turniere_liga.ausrichter = teams_liga.team_id
            WHERE saison = ?
            AND phase = 'ergebnis'
            AND art IN ('I', 'II', 'III')
            GROUP BY ausrichter
            ORDER BY anzahl DESC, RAND()
            LIMIT ?
        ";

        $teams = db::$db->query($sql, $saison, $limit)->esc()->fetch();
        
        $rang = 1;
        $vorher_rang = 1;
        $vorher_anz = 0;
        foreach ($teams as $team_id => $team) {
            if ($teams[$team_id]['anzahl'] >= $vorher_anz) {
                $teams[$team_id]['platz'] = $vorher_rang;
            } else {
                $teams[$team_id]['platz'] = $rang;
            }

            $vorher_anz = $teams[$team_id]['anzahl'];
            $vorher_rang = $teams[$team_id]['platz'];
            $rang++;
        }

        return $teams;
    }

    /**
     * Erfolgreiche Turniere nach Ort
     *
     * @param int $saison
     * @param int $limit
     * @return array
     */
    public static function get_max_turnierorte(int $saison = Config::SAISON, int $limit = 999): array
    {   
        $sql = "
            SELECT turniere_details.ort, COUNT(*) AS anzahl
            FROM turniere_liga
            LEFT JOIN turniere_details ON turniere_liga.turnier_id = turniere_details.turnier_id
            WHERE saison = ?
            AND phase = 'ergebnis'
            AND art IN ('I', 'II', 'III')
            GROUP BY turniere_details.ort
            ORDER BY anzahl DESC, RAND()
            LIMIT ?
        ";

        $orte = db::$db->query($sql, $saison, $limit)->esc()->fetch();
        
        $rang = 1;
        $vorher_rang = 1;
        $vorher_anz = 0;
        foreach ($orte as $ort_id => $ort) {
            if ($orte[$ort_id]['anzahl'] >= $vorher_anz) {
                $orte[$ort_id]['platz'] = $vorher_rang;
            } else {
                $orte[$ort_id]['platz'] = $rang;
            }

            $vorher_anz = $orte[$ort_id]['anzahl'];
            $vorher_rang = $orte[$ort_id]['platz'];
            $rang++;
        }

        return $orte;
    }

}