<?php
namespace App\Service\Team;

use Config;
use db;

class Teamstats
{
    private int $team_id;
    private int $saison;
    private array|null $data;
    public function __construct(int $team_id, int $saison = Config::SAISON)
    {
        $this->team_id = $team_id;
        $this->saison = $saison;
        $this->data = $this->get_spiele();
    }

    private function get_spiele(): array|null {
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

        $data = db::$db->query($sql, $this->team_id, $this->saison, $this->team_id, $this->team_id)->esc()->fetch();

        $result = [];

        // tausche die Daten so, dass das zu untersuchende Team immer in der ersten Stelle steht
        foreach ($data as $row) {
            if ($row['team_id_a'] != $this->team_id) {

                // Team IDs
                $row['team_id_b'] = $row['team_id_a'];
                $row['team_id_a'] = $this->team_id;

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

            array_push($result, $row);
        }

        return $result;
    }

    public function get_anzahl_spiele(): int
    {
        return count($this->data);
    }

    /**
     * @param int $mode schwaches team (0), starkes team (1), alle (2)
     * @return int[]
     */
    public function get_spiele_verteilung(int $mode = 2): array
    {
        $win = 0;
        $draw = 0;
        $loss = 0;
        $pwin = 0;
        $ploss = 0;

        foreach ($this->data as $spiel) {
            if ($spiel['stark'] != $mode && $mode != 2) continue;

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