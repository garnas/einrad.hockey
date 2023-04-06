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
            SELECT te.turnier_id, ergebnis, platz, td.ort, tl.datum
            FROM turniere_ergebnisse te
            INNER JOIN turniere_liga tl on te.turnier_id = tl.turnier_id
            INNER JOIN turniere_details td on te.turnier_id = td.turnier_id
            WHERE team_id = ?
            AND saison = ?
            AND art NOT LIKE 'final'
            ORDER BY ergebnis DESC
        ";

        return db::$db->query($sql, $this->team_id, $this->saison)->esc()->fetch();
    }

    public function get_anzahl_spiele(): int
    {
        return count($this->game_data);
    }

    public function get_anzahl_turniere(): int
    {
        return count($this->tournament_data);
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

    /**
     * Feststellung des hoechsten Siegs eines Teams.
     * Dabei gilt zuerst die Anzahl der selbst geschossenen Tore; bei Gleichheit ist die Anzahl der Gegentore (Differenz zwischen Toren und Gegentore) ausschlaggebend.
     *
     * @return array|null
     */
    public function get_hoechster_sieg(): array|null
    {
        $rs = NULL;
        $diff = 0;
        $goals = 0;
        foreach ($this->game_data as $id => $game) {
            if ($game['tore_a'] <= $game['tore_b']) continue; // Niederlage oder Unentschieden werden nicht betrachtet

            if (($game['tore_a'] > $goals) || ($game['tore_a'] == $goals && $diff > ($game['tore_a'] - $game['tore_b']))) {
                $diff = $game['tore_a'] - $game['tore_b'];
                $goals = $game['tore_a'];
                $rs = $id;
            }
        }

        return is_null($rs) ? $rs : $this->game_data[$rs];
    }

    /**
     * Feststellung der hoechsten Niederlage eines Teams.
     * Dabei gilt zuerst die Anzahl der Gegentore; bei Gleichheit ist die Anzahl der Tore (Differenz zwischen Gegentoren und Toren) ausschlaggebend.
     *
     * @return array|null
     */
    public function get_hoechste_niederlage(): array|null
    {
        $rs = NULL;
        $diff = 0;
        $goals = 0;

        foreach ($this->game_data as $id => $game) {
            if ($game['tore_a'] >= $game['tore_b']) continue; // Siege oder Unentschieden werden nicht beachtet

            if (($goals < $game['tore_b']) || ($goals == $game['tore_b'] && $diff > ($game['tore_b'] - $game['tore_a']))) {
                $diff = $game['tore_b'] - $game['tore_a'];
                $goals = $game['tore_b'];
                $rs = $id;
            }
        }

        return is_null($rs) ? $rs : $this->game_data[$rs];
    }

    public function get_bestes_turnier(): array|null
    {
        return empty($this->tournament_data) ? NULL : $this->tournament_data[0];
    }

    public function get_schlechtestes_turnier(): array|null
    {
        return empty($this->tournament_data) ? NULL : $this->tournament_data[max(array_keys($this->tournament_data))];
    }

    public function get_gegner(bool $ligateams_only = false): array|null
    {
        $rs = [];
        foreach ($this->game_data as $game) {
            $id = $game['team_id_b'];

            if (!key_exists($id, $rs)) {
                $ligateam = $game['tb_ligateam'] == 'Ja';

                if ($ligateams_only && !$ligateam) continue;
                $rs[$id] = array('games'=>0, 'win'=>0, 'draw'=>0, 'loss'=>0, 'goals'=>0, 'goals_against'=>0);
            }

            $h = $game['tore_a'];
            $g = $game['tore_b'];

            if ($this->is_win($h, $g)) {
                $rs[$id]['win'] += 1;
                $rs[$id]['goals'] += $h;
                $rs[$id]['goals_against'] += $g;
            }

            if ($this->is_draw($h, $g)) {
                $rs[$id]['draw'] += 1;
                $rs[$id]['goals'] += $h;
                $rs[$id]['goals_against'] += $g;
            }

            if ($this->is_loss($h, $g)) {
                $rs[$id]['loss'] += 1;
                $rs[$id]['goals'] += $h;
                $rs[$id]['goals_against'] += $g;
            }

            $rs[$id]['games'] += 1;
        }

        uasort($rs, function($a, $b) {
            $diff = ($b['win'] - $b['loss']) - ($a['win'] - $a['loss']);
            if ($diff != 0) return $diff;
            $diff = $b['win'] - $a['win'];
            if ($diff != 0) return $diff;
            $diff = $a['loss'] - $b['loss'];
            if ($diff != 0) return $diff;
            $diff = $b['draw'] - $a['draw'];
            if ($diff != 0) return $diff;
            $diff = ($b['goals'] - $b['goals_against']) - ($a['goals'] - $a['goals_against']);
            if ($diff != 0) return $diff;
            $diff = $a['goals'] - $b['goals'];
            if ($diff != 0) return $diff;
            return $b['goals_against'] - $a['goals_against'];
        });

        return $rs;
    }

    /**
     * Feststellung der Teams, gegen die Teams, gegen die am haeufigsten verloren wurde.
     * Dabei gilt zuerst die Anzahl der Niederlagen; bei Gleichheit ist die Anzahl der Siege und Unentschieden (Differenz) ausschlaggebend.
     *
     * @return array|null
     */
    public function get_angstgegner(): array|null
    {
        // Erhalte die Zusammenfassung gegen alle Gegner
        $teams = $this->get_gegner();

        // Erhalte das beste Wertepaar
        $losses = 0;
        $diff = 0;
        foreach ($teams as $team) {
            if ($team['win'] + $team['draw'] >= $team['loss']) continue; // Mehr (Siege und Untentschieden) als Niederlagen

            if ($team['loss'] > $losses || $team['loss'] == $losses && ($team['loss'] - $team['draw'] - $team['win']) > $diff) {
                $losses = $team['loss'];
                $diff = $team['loss'] - $team['draw'] - $team['win'];
            }
        }

        if ($losses == 0 && $diff == 0) return null; // Es wurde kein Angstgegner gefunden

        // Finde alle Gegner, die auf das gefundene Wertepaar zutreffen
        $rs = [];
        foreach ($teams as $team_id => $team) {
            if ($team['loss'] == $losses && ($team['loss'] - $team['draw'] - $team['win']) == $diff) {
                $rs[$team_id] = $team;
                $rs[$team_id]['team_id'] = $team_id;
            }
        }

        uasort($rs, function($a, $b) {
            $a_diff = $a['goals'] - $a['goals_against'];
            $b_diff = $b['goals'] - $b['goals_against'];
            return $a_diff - $b_diff;
        });

        return $rs;
    }

    /**
     * Feststellung der Teams, gegen die am haeufigsten gewonnen wurde.
     * Dabei gilt zuerst die Anzahl der Siege; bei Gleichheit ist die Anzahl der Niederlagen und Unentschieden (Differenz) ausschlaggebend.
     *
     * @return array|null
     */
    public function get_lieblingsgegner(): array|null
    {
        // Erhalte die Zusammenfassung gegen alle Gegner
        $teams = $this->get_gegner();

        // Erhalte das beste Wertepaar
        $wins = 0;
        $diff = 0;
        foreach ($teams as $team) {
            if ($team['loss'] + $team['draw'] >= $team['win']) continue; // Mehr (Niederlagen und Untentschieden) als Siege

            if ($team['win'] > $wins || $team['win'] == $wins && ($team['win'] - $team['draw'] - $team['loss']) > $diff) {
                $wins = $team['win'];
                $diff = $team['win'] - $team['draw'] - $team['loss'];
            }
        }

        if ($wins == 0 && $diff == 0) return null; // Es wurde kein Lieblingsgegner gefunden

        // Finde alle Gegner, die auf das gefundene Wertepaar zutreffen
        $rs = [];
        foreach ($teams as $team_id => $team) {
            if ($team['win'] == $wins && ($team['win'] - $team['draw'] - $team['loss']) == $diff) {
                $rs[$team_id] = $team;
                $rs[$team_id]['team_id'] = $team_id;
            }
        }

        uasort($rs, function($a, $b) {
            $a_diff = $a['goals'] - $a['goals_against'];
            $b_diff = $b['goals'] - $b['goals_against'];
            return $b_diff - $a_diff;
        });

        return $rs;
    }
}