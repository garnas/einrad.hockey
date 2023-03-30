<?php
namespace App\Service\Team;

use Config;
use db;

class Teamstats
{
    private int $team_id;
    private int $saison;
    private array|null $data;

    public const ALLE = 0;
    public const STARK = 1;
    public const SCHWACH = 2;


    public function __construct(int $team_id, int $saison = Config::SAISON)
    {
        $this->team_id = $team_id;
        $this->saison = $saison;
        $this->data = $this->get_spiele();
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

    public function get_anzahl_spiele(): int
    {
        return count($this->data);
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

        foreach ($this->data as $spiel) {
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
}