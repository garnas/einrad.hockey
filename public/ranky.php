<?php
namespace App\Event\Turnier;


use App\Repository\Team\TeamRepository;
use Env;
use Ranking;
use Tabelle;
use Team;
use Html;

require_once '../init.php';

//$rankings = Ranking::get_all_spiele();
//
//Ranking::reset_ratings();
//foreach ($rankings as $rank) {
//    Ranking::calculate_glicko_2($rank);
//    Ranking::calculate_elo($rank);
//    Ranking::persist_ranking($rank);
//    Ranking::persist_ranking_elo($rank);
//}

$rankings = Ranking::get_all_spiele();

foreach ($rankings as $rank) {
    Ranking::calculate_glicko_2($rank, $rankings);
    Ranking::calculate_elo($rank, $rankings);
}

$akt_spieltag = Tabelle::get_aktuellen_spieltag();
$rangtabelle = Tabelle::get_rang_tabelle($akt_spieltag);

$teams = TeamRepository::get()->activeLigaTeams();

foreach ($teams as $team) {
    $id = $team->id();
    $glicko[] = [
        "score" => Ranking::get_rating_from_rankings($id, $rankings)->getRating(),
        "teamname" => $team->getName(),
        "id" => $id
    ];
    $elo[] = [
        "score" => Ranking::get_rating_elo_from_rankings($id, $rankings)->getRating(),
        "teamname" => $team->getName(),
        "id" => $id
    ];
    $rang[] = [
        "score" => $rangtabelle[$id]["avg"],
        "teamname" => $team->getName(),
        "id" => $id
    ];
}
$sort = static function (&$arg) {
    $score = array_column($arg, 'score');
    array_multisort($score, SORT_DESC, $arg);
};

$sort($glicko);
$sort($elo);
$sort($rang);

foreach ($glicko as $key => $_) {
    $multi_rankings[] = [
        "glicko" => $glicko[$key],
        "elo" => $elo[$key],
        "rang" => $rang[$key],
    ];
}

$colors = ['#e6194B', '#3cb44b', '#ffe119', '#4363d8', '#f58231', '#911eb4', '#42d4f4', '#f032e6', '#bfef45', '#fabed4', '#469990', '#dcbeff', '#9A6324', '#fffac8', '#800000', '#aaffc3', '#808000', '#ffd8b1', '#6d6d9e', '#a9a9a9'];
$colors = array_merge($colors, $colors, $colors, $colors, $colors);
foreach ($rangtabelle as $id => $r) {
    $team_to_color[$id] = $colors[$r["rang"] - 1];
}
$get_background_color = static function ($ranking, $type) use ($team_to_color) {
    return "background-color: " . $team_to_color[$ranking[$type]["id"]] . ";";
};
//foreach ($rankings as $key => $ranking) {
//    $rankings[$key]["delta"] = $ranking["rang"] - $key - 1;
//}
//$glicko = array_column($rankings, 'glicko');
//array_multisort($glicko, SORT_DESC, $rankings);
//foreach ($rankings as $key => $ranking) {
//    $rankings[$key]["delta"] = $ranking["rang"] - $key - 1;
//}
////arsort($rankings);

Html::$page_width = "100%";
include Env::BASE_PATH . '/templates/header.tmp.php';

?>
<form method="get">
    <p>Saisons
        <input type="number" name="threshold_saison" min="0" max="5"
               value="<?= Ranking::get_total_seasons_for_calc() ?>">
    </p>
    <p>
        <input class="w3-check" type="checkbox" min="0" max="1" step="any"
               name="with_nl" <?= Ranking::get_with_nl_teams() ? "checked" : "" ?>>
        NL-Teams einbeziehen
    </p>
    <p>Glicko/Elo K
        <input type="number" name="default_rating" value="<?= Ranking::get_first_rating() ?>">
    </p>
    <p>Glicko-Volatility
        <input type="number" name="volatility" min="0.01" max="0.3" step="0.01"
               value="<?= Ranking::get_first_volatility() ?>">
    </p>
    <p>Glicko-Deviaton
        <input type="number" name="deviation" min="0" max="400" value="<?= Ranking::get_first_deviation() ?>">
    </p>
    <p class="w3-tiny"><i>Glicko-Tau</i>
        <input type="number" min="0" max="1" step="0.01" name="tau"
               value="<?= Ranking::get_tau() ?>">
    </p>
    <p class="w3-tiny"><i>Glicko-Tol</i>
        <input type="number" min="0" max="1" step="any" name="tol"
               value="<?= Ranking::get_tol() ?>">
    </p>
    <p>
        <input type="submit" class="w3-button w3-primary" value="CALCULATE!" name="change">
    </p>
</form>
<form method="get">
    <p>
        <input type="submit" value="Reset" class="w3-button w3-secondary">
    </p>
</form>

<p>Anzahl Spiele: <?= count($rankings) ?></p>
<div class="w3-responsive w3-card table-wrap">
    <table class="w3-table w3-striped w3-centered sortable">
        <thead class="w3-primary">
        <tr>
            <th class="num"><b>
                    <button class="w3-center">
                        Glicko
                        <span aria-hidden="true"></span>
                    </button>
                </b></th>
            <th><b>
                    <button class="w3-center">
                        Teamname
                        <span aria-hidden="true"></span>
                    </button>
                </b></th>
            <th class="num"><b>

                    <button class="w3-center">
                        Rang
                        <span aria-hidden="true"></span>
                    </button>
                </b></th>
            <th><b>
                    <button class="w3-center">
                        Teamname
                        <span aria-hidden="true"></span>
                    </button>
                </b></th>
            <th class="num"><b>
                    <button class="w3-center">
                        Elo
                        <span aria-hidden="true"></span>
                    </button>
                </b></th>
            <th><b>
                    <button class="w3-center">
                        Teamname
                        <span aria-hidden="true"></span>
                    </button>
                </b></th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($multi_rankings as $multi_ranking) { ?>
            <tr>
                <td style="<?= $get_background_color($multi_ranking, "glicko") ?>"
                    class="num w3-center"><?= round($multi_ranking["glicko"]["score"], 1) ?></td>
                <td style="white-space: nowrap;<?= $get_background_color($multi_ranking, "glicko") ?>"><?= $multi_ranking["glicko"]["teamname"] ?></td>
                <td style="<?= $get_background_color($multi_ranking, "rang") ?>"
                    class="num w3-center"><?= round($multi_ranking["rang"]["score"], 1) ?></td>
                <td style="white-space: nowrap;<?= $get_background_color($multi_ranking, "rang") ?>"><?= $multi_ranking["rang"]["teamname"] ?></td>
                <td style="<?= $get_background_color($multi_ranking, "elo") ?>"
                    class="num w3-center"><?= round($multi_ranking["elo"]["score"], 1) ?></td>
                <td style="white-space: nowrap;<?= $get_background_color($multi_ranking, "elo") ?>"><?= $multi_ranking["elo"]["teamname"] ?></td>
            </tr>
        <?php } //end foreach
        ?>
        </tbody>
    </table>
</div>
<br>
<div class="w3-responsive w3-card table-wrap">
    <table class="w3-table w3-striped w3-centered">
        <thead class="w3-primary">
        </tr>
        <th>Datum</th>
        <th>&#9650; Elo<br>&#9650; Glicko (Dev/Vol)</th>
        <th>Team A<br>Tore</th>
        <th>Team B<br>Tore</th>
        <th>&#9650; Elo<br>&#9650; Glicko (Dev/Vol)</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($rankings as $ranking): ?>
            <tr>
                <td style="white-space: nowrap;"><?= $ranking->datum ?></td>
                <td style="white-space: nowrap;"><?=round($ranking->rating_a_elo)?> <?= $ranking->delta_a >= 0 ? "+" : "" ?><?= round($ranking->delta_a_elo) ?>
                    <br>
                    <?=round($ranking->rating_a)?> <?= $ranking->delta_a >= 0 ? "+" : "" ?><?= round($ranking->delta_a) ?>
                    (<?= round($ranking->rating_a_deviation) ?>/<?= round($ranking->rating_a_volatility, 5) ?>)
                </td>
                <td style="white-space: nowrap;"><?= Team::id_to_name($ranking->team_id_a) ?><br><?= $ranking->tore_a ?>
                </td>
                <td style="white-space: nowrap;"><?= Team::id_to_name($ranking->team_id_b) ?><br><?= $ranking->tore_b ?>
                </td>
                <td style="white-space: nowrap;"><?=round($ranking->rating_b_elo)?> <?= $ranking->delta_b >= 0 ? "+" : "" ?><?= round($ranking->delta_b_elo) ?>
                    <br>
                    <?=round($ranking->rating_b)?> <?= $ranking->delta_b >= 0 ? "+" : "" ?><?= round($ranking->delta_b,) ?>
                    (<?= round($ranking->rating_b_deviation) ?>/<?= round($ranking->rating_b_volatility, 5) ?>)
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>