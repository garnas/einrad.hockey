<?php
namespace App\Event\Turnier;


use App\Repository\Team\TeamRepository;
use Env;
use db;
use Ranking;
use Tabelle;
use Config;

require_once '../init.php';

$rankings_by_team = Ranking::get_spiele_by_team(30);

//Ranking::reset_ratings();
$rankings_glicko = Ranking::get_all_spiele(false);
foreach ($rankings_glicko as $rank1) {
//    Ranking::calculate_glicko_2($rank1);
}
$rankings_elo = Ranking::get_all_spiele(false);
foreach ($rankings_elo as $rank2) {
//    Ranking::calculate_elo($rank2);
}

$akt_spieltag = Tabelle::get_aktuellen_spieltag();
$rangtabelle = Tabelle::get_rang_tabelle($akt_spieltag);

$teams = TeamRepository::get()->activeLigaTeams();
foreach ($teams as $team) {
    $id = $team->id();
//    $rankings[] = [
//        "glicko" => Ranking::get_rank($id),
//        "elo" => Ranking::get_rank_elo($id),
//        "teamname" => $team->getName(),
//        "team_id" => $id,
//        "rang" => $rangtabelle[$id]["rang"]
//    ];
    $glicko[] = [
        "score" => Ranking::get_rank_from_rankings($id, $rankings_glicko),
        "teamname" => $team->getName(),
        "color" => $team->getDetails()->getTrikotFarbe1(),
        "id" => $id
    ];
    $elo[] = [
        "score" => Ranking::get_rank_from_rankings($id, $rankings_elo, for_elo: true),
        "teamname" => $team->getName(),
        "color" => $team->getDetails()->getTrikotFarbe1(),
        "id" => $id
    ];
    $rang[] = [
        "score" => $rangtabelle[$id]["avg"],
        "teamname" => $team->getName(),
        "color" => $team->getDetails()->getTrikotFarbe1(),
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


include Env::BASE_PATH . '/templates/header.tmp.php';

?>
<form method="get">
    <p>Saisons
        <input type="number" name="threshold_saison" value="<?= $_GET["threshold_saison"] ?? Ranking::TOTAL_SEASONS_FOR_CALC ?>">
    </p>

    <p>Glicko/Elo K
        <input type="number" name="default_rating" value="<?= $_GET["default_rating"] ?? Ranking::RATING_DEFAULT ?>">
    </p>

    <p>Glicko-Volatility
        <input type="number" name="volatility" min="0.01" max="0.3" step="" value="<?= $_GET["volatility"] ?? Ranking::VOLATILITY_DEFAULT ?>">
    </p>

    <p>Glicko-Deviaton
        <input type="number" name="deviation" min="0" max="400" value="<?= $_GET["deviation"] ?? Ranking::DEVIATION_DEFAULT ?>">
    </p>
    <p class="w3-text-grey">Glicko-Tau
        <input class="w3-text-grey" type="number" min="0" max="1" step="any" name="tau" value="<?= $_GET["tau"] ?? Ranking::TAU ?>">
    </p>
    <p class="w3-text-grey">Glicko-Tol
        <input class="w3-text-grey" type="number" min="0" max="1" step="any" name="tol" value="<?= $_GET["tol"] ?? Ranking::TOL ?>">
    </p>
    <p>
        <input type="submit" value="Ändern">
    </p>
</form>
<form method="get">
    <p>
        <input type="submit" value="Reset">
    </p>
</form>
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
        </tr>
        </thead>
        <tbody>
        <?php foreach ($multi_rankings as $mulit_ranking) { ?>
            <tr>
                <td style="<?= $get_background_color($mulit_ranking, "glicko") ?>"
                    class="num w3-center"><?= round($mulit_ranking["glicko"]["score"], 1) ?></td>
                <td style="white-space: nowrap;<?= $get_background_color($mulit_ranking, "glicko") ?>"><?= $mulit_ranking["glicko"]["teamname"] ?></td>
                <td style="<?= $get_background_color($mulit_ranking, "elo") ?>"
                    class="num w3-center"><?= round($mulit_ranking["elo"]["score"], 1) ?></td>
                <td style="white-space: nowrap;<?= $get_background_color($mulit_ranking, "elo") ?>"><?= $mulit_ranking["elo"]["teamname"] ?></td>
                <td style="<?= $get_background_color($mulit_ranking, "rang") ?>"
                    class="num w3-center"><?= round($mulit_ranking["rang"]["score"], 1) ?></td>
                <td style="white-space: nowrap;<?= $get_background_color($mulit_ranking, "rang") ?>"><?= $mulit_ranking["rang"]["teamname"] ?></td>
            </tr>
        <?php } //end foreach
        ?>
        </tbody>
    </table>
</div>