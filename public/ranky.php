<?php
namespace App\Event\Turnier;


use App\Repository\Team\TeamRepository;
use Env;
use db;
use Ranking;
use Tabelle;
use Config;

require_once '../init.php';


$rankings = Ranking::get_all_spiele(false);
$rankings_by_team = Ranking::get_spiele_by_team(30);

//Ranking::reset_ratings();
//foreach ($rankings as $rank) {
//
//    Ranking::calculate_glicko_2($rank);
//    Ranking::persist_ranking($rank);
//
//    Ranking::calculate_elo($rank);
//    Ranking::persist_ranking_elo($rank);
//}

$teams = TeamRepository::get()->activeLigaTeams();
$rankings = [];

$akt_spieltag = Tabelle::get_aktuellen_spieltag(Config::SAISON);
$rangtabelle = Tabelle::get_rang_tabelle($akt_spieltag);
//db::debug($rangtabelle);
foreach ($teams as $team) {
    $id = $team->id();
    $rankings[] = [
        "glicko" => Ranking::get_rank($id),
        "elo" => Ranking::get_rank_elo($id),
        "teamname" => $team->getName(),
        "team_id" => $id,
        "rang" => $rangtabelle[$id]["rang"]
    ];
    $rankings_glicko[] = [
        "score" => Ranking::get_rank($id),
        "teamname" => $team->getName(),
    ];
    $rankings_elo[] = [
        "score" => Ranking::get_rank_elo($id),
        "teamname" => $team->getName(),
    ];
    $ranking_rang[] = [
        "score" => $rangtabelle[$id]["avg"],
        "teamname" => $team->getName(),
    ];
}
$sort = static function (&$arg) {
    $score = array_column($arg, 'score');
    array_multisort($score, SORT_DESC, $arg);
};

$sort($rankings_glicko);
$sort($rankings_elo);
$sort($ranking_rang);

foreach ($rankings_glicko as $key => $_) {
    $multi_rankings[] = [
        "glicko" => $rankings_glicko[$key],
        "elo" => $rankings_elo[$key],
        "rang" => $ranking_rang[$key],
    ];
}
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
                </b></th>            <th class="num"><b>

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
                </b></th>        </tr>
        </thead>
        <tbody>
        <?php foreach ($multi_rankings as $mulit_ranking) { ?>
            <tr>
                <td class="num w3-center"><?= round($mulit_ranking["glicko"]["score"], 1) ?></td>
                <td style="white-space: nowrap;"><?= $mulit_ranking["glicko"]["teamname"] ?></td>
                <td class="num w3-center"><?= round($mulit_ranking["elo"]["score"], 1) ?></td>
                <td style="white-space: nowrap;"><?= $mulit_ranking["elo"]["teamname"] ?></td>
                <td class="num w3-center"><?= round($mulit_ranking["rang"]["score"], 1) ?></td>
                <td style="white-space: nowrap;"><?= $mulit_ranking["rang"]["teamname"] ?></td>
            </tr>
        <?php } //end foreach
        ?>
        </tbody>
    </table>
</div>