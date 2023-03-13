<?php

class Teamstats
{
    private static function get_spiele(int $team_id, $saison = Config::SAISON): array {
        $sql = "
            SELECT spiele.*, ta.ligateam AS ta_ligateam, tb.ligateam AS tb_ligateam, IF(team_id_a = ?, TRUE, FALSE) AS stark
            FROM spiele
                INNER JOIN turniere_liga tur on spiele.turnier_id = tur.turnier_id
                INNER JOIN teams_liga ta on spiele.team_id_a = ta.team_id
                INNER JOIN teams_liga tb on spiele.team_id_b = tb.team_id
            WHERE tur.saison = ?
              AND tur.phase = 'ergebnis'
              AND tur.datum < CURDATE()
              AND (team_id_a = ? OR team_id_b = ?);
        ";

        $data = db::$db->query($sql, $team_id, $saison, $team_id, $team_id)->esc()->fetch();

        // tausche die Daten so, dass das zu untersuchende Team immer in der ersten Stelle steht
        foreach ($data as $row) {
            if ($row['team_id_a'] == $team_id) continue;
            // Team IDs
            $row['team_id_b'] = $row['team_id_a'];
            $row['team_id_a'] = $team_id;

            // Spielergebnis
            $tmp = $row['tore_a'];
            $row['tore_a'] = $row['tore_b'];
            $row['tore_b'] = $tmp;

            // Penaltyergebnis
            $tmp = $row['penalty_a'];
            $row['penalty_a'] = $row['penalty_b'];
            $row['penalty_b'] = $tmp;

            // Ligateam
            $tmp = $row['ta_ligateam'];
            $row['ta_ligateam'] = $row['tb_ligateam'];
            $row['tb_ligateam'] = $tmp;
        }

        return $data;
    }

    public static function get_anzahl_spiele(int $team_id, $saison = Config::SAISON): int
    {
        $data = Teamstats::get_spiele($team_id, $saison);
        return count($data);
    }

    public static function get_spiele_verteilung(int $team_id, $saison = Config::SAISON): array
    {
        $data = Teamstats::get_spiele($team_id, $saison);

        $win = 0;
        $draw = 0;
        $loss = 0;
        $pwin = 0;
        $ploss = 0;
        foreach ($data as $spiel) {
            if ($spiel['tore_a'] == $spiel['tore_b']) {
                if (is_null($spiel['penalty_a'])) {
                    $draw += 1;
                    continue;
                }
                if ($spiel['penalty_a'] > $spiel['penalty_b']) {
                    $pwin += 1;
                } else {
                    $ploss += 1;
                }
                continue;
            }

            if ($spiel['tore_a'] > $spiel['tore_b']) {
                $win += 1;
                continue;
            }

            $loss += 1;
        }

        return array("win" => $win, "draw" => $draw, "loss"=>$loss, "pwin"=>$pwin, "ploss"=>$ploss);
    }
}