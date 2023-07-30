<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../init.php';
// Turnier-ID

$teamliste = [
    1 => "Deutschland schwarz",
    2 => "TV Lilienthal Moorlichter",
    3 => "Deutschland rot",
    4 => "Dresdner Einradlöwen",
    5 => "Deutschland gold",
    6 => "TV Lilienthal Moorteufel",
    7 => "MJC Trier",
    8 => "Lucky Shots",
    9 => "Team Steyr Unicycling",
];
$schiri = [
    1 => "Sch",
    2 => "Mlr",
    3 => "Rot",
    4 => "Löw",
    5 => "Gol",
    6 => "Mtl",
    7 => "Tri",
    8 => "Lky",
    9 => "Sty",
];

require_once Env::BASE_PATH . '/logic/spielplan_euhc.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "EUHC Spielplan";
//Html::$content = "Der Spielplan für das Einradhockey-Turnier in " . $spielplan->turnier->get_ort()
//                    . " am " . date("d.m.Y", strtotime($spielplan->turnier->get_datum()));
include Env::BASE_PATH . '/templates/header.tmp.php';
//include '../../templates/spielplan/spielplan_titel.tmp.php';
include Env::BASE_PATH . '/templates/spielplan/spielplan_turniertabelle_euhc.tmp.php'; // Abschlusstabelle
include Env::BASE_PATH . '/templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
?>

<!--<h2 class="w3-text-secondary w3-margin-top">Teamliste</h2>-->
<!--<div class="w3-section" style="max-width: 600px">-->
<!--    <div class="w3-responsive w3-card-4">-->
<!--        <table class="w3-table w3-striped w3-centered" style="white-space: nowrap;">-->
<!--            <tr class="w3-primary">-->
<!--                <th>--><?php //= Html::icon("info_outline") ?><!--<br>Team-ID</th>-->
<!--                <th>--><?php //= Html::icon("group") ?><!--<br>Team</th>-->
<!--            </tr>-->
<!--            --><?php //foreach ($spielplan->teamliste as $team_id => $team) { ?>
<!--                <tr>-->
<!--                    <td>--><?php //= $team_id ?><!--</td>-->
<!--                    <td>--><?php //= $team->teamname ?><!--</td>-->
<!--                </tr>-->
<!--            --><?php //}//end foreach?>
<!--        </table>-->
<!--    </div>-->
<!--</div>-->

<?php
$spielplan->spiele = $spiele_mittwoch;
?><h1 class="w3-text-secondary">Mittwoch, 02.08.2023</h1><?php
include Env::BASE_PATH . '/templates/spielplan/spielplan_spiele_euhc.tmp.php'; // Spiele
$spielplan->spiele = $spiele_donnerstag;
?><h1 class="w3-text-secondary">Donnerstag, 03.08.2023</h1><?php
include Env::BASE_PATH . '/templates/spielplan/spielplan_spiele_euhc.tmp.php'; // Spiele
$spielplan->spiele = $spiele_backup;
include Env::BASE_PATH . '/templates/footer.tmp.php';

