<?php
namespace App\Service\Team;

use Config;
use db;

class Teamstats
{
    private int $team_id;
    private int $saison;
    private array|null $game_data;
    private array|null $tournament_data;

    public const ALLE = 0;
    public const STARK = 1;
    public const SCHWACH = 2;


    public function __construct(int $team_id, int $saison = Config::SAISON)
    {
        $this->team_id = $team_id;
        $this->saison = $saison;
        $this->game_data = $this->get_spiele();
        $this->tournament_data = $this->get_turniere();
    }

    private function get_spiele(): array|null {
        $sql = "
            SELECT spiele.*, ta.ligateam AS ta_ligateam, tb.ligateam AS tb_ligateam
            FROM spiele
                INNER JOIN turniere_liga tur on spiele.turnier_id = tur.turnier_id
                INNER JOIN teams_liga ta on spiele.team_id_a = ta.team_id
                INNER JOIN teams_liga tb on spiele.team_id_b = tb.team_id
            WHERE tur.saison = ?
              AND tur.phase = 'ergebnis'
              AND tur.datum < CURDATE()
              AND (team_id_a = ? OR team_id_b = ?);
        ";

        $data = db::$db->query($sql, $this->saison, $this->team_id, $this->team_id)->esc()->fetch();

        $result = [];

        // erweitere die Daten und stelle sie so um, dass am Ende das gesuchte Team immer als team_a gelistet ist
        foreach ($data as $game) {
            if ($game['team_id_a'] == $this->team_id) {
                $entry =
                    array(
                        'turnier_id' => $game['turnier_id'],
                        'spiel_id' => $game['spiel_id'],
                        'team_id_a' => $game['team_id_a'],
                        'team_id_b' => $game['team_id_b'],
                        'schiri_team_id_a' => $game['schiri_team_id_a'],
                        'schiri_team_id_b' => $game['schiri_team_id_b'],
                        'tore_a' => $game['tore_a'],
                        'tore_b' => $game['tore_b'],
                        'penalty_a' => $game['penalty_a'],
                        'penalty_b' => $game['penalty_b'],
                        'ta_ligateam' => $game['ta_ligateam'],
                        'tb_ligateam' => $game['tb_ligateam'],
                        'stark' => true,
                        'schwach' => false
                    );
            } else {
                $entry =
                    array(
                        'turnier_id' => $game['turnier_id'],
                        'spiel_id' => $game['spiel_id'],
                        'team_id_a' => $game['team_id_b'],
                        'team_id_b' => $game['team_id_a'],
                        'schiri_team_id_a' => $game['schiri_team_id_a'],
                        'schiri_team_id_b' => $game['schiri_team_id_b'],
                        'tore_a' => $game['tore_b'],
                        'tore_b' => $game['tore_a'],
                        'penalty_a' => $game['penalty_b'],
                        'penalty_b' => $game['penalty_a'],
                        'ta_ligateam' => $game['tb_ligateam'],
                        'tb_ligateam' => $game['ta_ligateam'],
                        'stark' => false,
                        'schwach' => true
                    );
            }

            array_push($result, $entry);

        }

        return $result;
    }

    private function get_turniere(): array|null {
        $sql = "
            SELECT te.turnier_id, ergebnis, platz
            FROM turniere_ergebnisse te
                INNER JOIN turniere_liga tl on te.turnier_id = tl.turnier_id
            WHERE team_id = ?
              AND saison = ?
        ";

        return db::$db->query($sql, $this->team_id, $this->saison)->esc()->fetch();
    }

    public function get_anzahl_spiele(): int
    {
        return count($this->game_data);
    }

    private function is_win(int $h, int $g): bool
    {
        return $h > $g;
    }

    private function is_loss(int $h, int $g): bool
    {
        return $h < $g;
    }

    private function is_draw(int $h, int $g): bool
    {
        return $h == $g;
    }

    private function is_penalty_win(int|null $h, int|null $g): bool
    {
        if (is_null($h)) return false;
        return $h > $g;
    }

    private function is_penalty_loss(int|null $h, int|null $g): bool
    {
        if (is_null($h)) return false;
        return $h < $g;
    }

    /**
     * @return int[]
     */
    public function get_verteilung_spiele(int $mode): array
    {
        $win = 0;
        $draw = 0;
        $loss = 0;
        $pwin = 0;
        $ploss = 0;
        $games = 0;

        foreach ($this->game_data as $spiel) {
            if (!(($mode == self::STARK && $spiel['stark']) || ($mode == self::SCHWACH && $spiel['schwach']) || $mode == self::ALLE)) continue;

            $h = $spiel['tore_a'];
            $g = $spiel['tore_b'];
            $ph = $spiel['penalty_a'];
            $pg = $spiel['penalty_b'];

            if ($this->is_win($h, $g)) $win += 1;
            if ($this->is_draw($h, $g)) $draw += 1;
            if ($this->is_loss($h, $g)) $loss +=1;
            if ($this->is_penalty_win($ph, $pg)) $pwin += 1;
            if ($this->is_penalty_loss($ph, $pg)) $ploss += 1;

            $games += 1;

        }

        return array("win" => $win, "draw" => $draw, "loss"=>$loss, "pwin"=>$pwin, "ploss"=>$ploss, "games"=>$games);
    }

    public function get_verteilung_tore(int $mode): array
    {
        $tore = 0;
        $gegentore = 0;

        foreach ($this->game_data as $spiel) {
            if (!(($mode == self::STARK && $spiel['stark']) || ($mode == self::SCHWACH && $spiel['schwach']) || $mode == self::ALLE)) continue;
            $tore += $spiel['tore_a'];
            $gegentore += $spiel['tore_b'];
        }

        return array('goals' => $tore, 'goals_against' => $gegentore, 'diff' => $tore - $gegentore);
    }

    public function get_hoechster_sieg(): array|null
    {
        // 1. Differenz zwischen Tore und Gegentore
        // 2. Anzahl Tore
        $rs = NULL;
        $cmp = 0;
        $goals = 0;
        foreach ($this->game_data as $id=> $spiel) {
            $diff = $spiel['tore_a'] - $spiel['tore_b'];
            if ($diff <= 0) continue;

            if (($diff > $cmp) || ($diff == $cmp && $spiel['tore_a'] > $goals)) {
                $goals = $spiel['tore_a'];
                $cmp = $diff;
                $rs = $id;
            }
        }

        return is_null($rs) ? $rs : $this->game_data[$rs];
    }

    public function get_hoechste_niederlage(): array|null
    {
        // 1. Differenz zwischen Tore und Gegentore
        // 2. Anzahl Gegentore
        $rs = NULL;
        $cmp = 0;
        $goals = 0;

        foreach ($this->game_data as $id=> $spiel) {
            $diff = $spiel['tore_a'] - $spiel['tore_b'];
            if ($diff >= 0) continue;

            if (($diff < $cmp) || ($diff == $cmp && $spiel['tore_b'] > $goals)) {
                $goals = $spiel['tore_a'];
                $cmp = $diff;
                $rs = $id;
            }
        }

        return is_null($rs) ? $rs : $this->game_data[$rs];
    }

    public function get_bestes_turnier(): array|null
    {
        $rs = NULL;
        $max = PHP_INT_MIN;
        foreach ($this->tournament_data as $id => $turnier) {
            if ($turnier['ergebnis'] > $max) {
                $max = $turnier['ergebnis'];
                $rs = $id;
            }
        }

        return is_null($rs) ? $rs : $this->tournament_data[$rs];
    }

    public function get_schlechtestes_turnier(): array|null
    {
        $rs = NULL;
        $min = PHP_INT_MAX;
        foreach ($this->tournament_data as $id => $turnier) {
            if ($turnier['ergebnis'] < $min) {
                $max = $turnier['ergebnis'];
                $rs = $id;
            }
        }

        return is_null($rs) ? $rs : $this->tournament_data[$rs];
    }
}