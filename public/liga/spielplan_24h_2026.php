<?php

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';


$turnier = nTurnier::get(1375);
// Gibt es einen Spielplan zu diesem Turnier?
if (!Spielplan::check_exist(1375)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}
$spielplan = new Spielplan_JgJ($turnier);

$map
    = [
        1 => '11:00',
        2 => '11:26',
        3 => '11:26',
        4 => '11:52',
        5 => '11:52',
        6 => '12:18',
        7 => '12:18',
        8 => '12:44',
        9 => '12:44',
        10 => '13:10',
        11 => '13:10',
        12 => '13:36',
        13 => '13:36',
        14 => '14:02',
        15 => '14:02',
        16 => '14:28',
        17 => '14:28',
        18 => '14:54',
        19 => '14:54',
        20 => '15:20',
        21 => '15:20',
        22 => '15:46',
        23 => '15:46',
        24 => '16:12',
        25 => '16:12',
        26 => '16:38',
        27 => '16:38',
        28 => '17:04',
        29 => '17:04',
        30 => '17:30',
        31 => '17:30',
        32 => '17:56',
        33 => '17:56',
        34 => '18:22',
        35 => '20:00',
        36 => '20:26',
        37 => '20:52',
        38 => '21:18',
        39 => '21:44',
        40 => '22:10',
        41 => '22:36',
        42 => '23:02',
        43 => '23:28',
        44 => '23:54',
        45 => '00:20',
        46 => '00:46',
        47 => '01:12',
        48 => '01:38',
        49 => '02:04',
        50 => '02:30',
        51 => '02:56',
        52 => '03:22',
        53 => '03:48',
        54 => '04:14',
        55 => '04:40',
    ];

foreach ($spielplan->spiele as $id => $spiel) {
    $spielplan->spiele[$id]["zeit"] = $map[$id];
}

$ostlandhalle_ids = [3,5,7,11,13,15,17,19,21,23,25,27,29,31,33];
$ostlandhalle_spiele = [];
$ballsporthalle_spiele = [];

foreach ($spielplan->spiele as $spielId => $spiel) {
    if (in_array($spielId, $ostlandhalle_ids, true)) {
        $ostlandhalle_spiele[$spielId] = $spiel;
    } else {
        $ballsporthalle_spiele[$spielId] = $spiel;
    }
}

$halbfinale = [
    1 => ['zeit' => '05:15', 'name' => 'Gruppe um Platz 9 - 11', 'team_a' => 'Vorrunde Platz 9', 'team_b' => 'Vorrunde Platz 11', 'schiri_a' => 'Vorrunde Platz 5', 'schiri_b' => 'Vorrunde Platz 8'],
    2 => ['zeit' => '05:45', 'name' => 'Halbfinale 1', 'team_a' => 'Vorrunde Platz 6', 'team_b' => 'Vorrunde Platz 7', 'schiri_a' => 'Vorrunde Platz 10', 'schiri_b' => 'Vorrunde Platz 1'],
    3 => ['zeit' => '06:15', 'name' => 'Halbfinale 2', 'team_a' => 'Vorrunde Platz 5', 'team_b' => 'Vorrunde Platz 8', 'schiri_a' => 'Vorrunde Platz 9', 'schiri_b' => 'Vorrunde Platz 11'],
    4 => ['zeit' => '06:45', 'name' => 'Gruppe um Platz 9 - 11', 'team_a' => 'Vorrunde Platz 10', 'team_b' => 'Vorrunde Platz 11', 'schiri_a' => 'Vorrunde Platz 1', 'schiri_b' => 'Vorrunde Platz 4'],
    5 => ['zeit' => '07:15', 'name' => 'Halbfinale 3', 'team_a' => 'Vorrunde Platz 2', 'team_b' => 'Vorrunde Platz 3', 'schiri_a' => 'Vorrunde Platz 6', 'schiri_b' => 'Vorrunde Platz 7'],
    6 => ['zeit' => '07:45', 'name' => 'Halbfinale 4', 'team_a' => 'Vorrunde Platz 1', 'team_b' => 'Vorrunde Platz 4', 'schiri_a' => 'Vorrunde Platz 2', 'schiri_b' => 'Vorrunde Platz 3'],
];

$finale = [
    11 => ['zeit' => '08:30', 'name' => 'Gruppe um Platz 9 - 11', 'team_a' => 'Vorrunde Platz 9', 'team_b' => 'Vorrunde Platz 10', 'schiri_a' => 'Vorrunde Platz 1', 'schiri_b' => 'Vorrunde Platz 2'],
    12 => ['zeit' => '09:00', 'name' => 'Spiel um Platz 7', 'team_a' => 'Verlierer HF 1', 'team_b' => 'Verlierer HF 2', 'schiri_a' => 'Vorrunde Platz 9', 'schiri_b' => 'Vorrunde Platz 10'],
    13 => ['zeit' => '09:30', 'name' => 'Spiel um Platz 5', 'team_a' => 'Gewinner HF 1', 'team_b' => 'Gewinner HF 2', 'schiri_a' => 'Verlierer HF 1', 'schiri_b' => 'Verlierer HF 2'],
    14 => ['zeit' => '10:00', 'name' => 'Spiel um Platz 3', 'team_a' => 'Verlierer HF 3', 'team_b' => 'Verlierer HF 4', 'schiri_a' => 'Gewinner HF 1', 'schiri_b' => 'Gewinner HF 2'],
    15 => ['zeit' => '10:30', 'name' => 'Spiel um Platz 1', 'team_a' => 'Gewinner HF 3', 'team_b' => 'Gewinner HF 4', 'schiri_a' => 'Verlierer HF 3', 'schiri_b' => 'Verlierer HF 4'],
];

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Spielplan | Einradhockey";
Html::$content = "Der Spielplan für das Einradhockey-Turnier in " . $spielplan->turnier->get_ort()
    . " am " . date("d.m.Y", strtotime($spielplan->turnier->get_datum()));

include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_titel.tmp.php';
include '../../templates/spielplan/spielplan_teamliste.tmp.php'; // Teamliste

// Hauptrunde
?><h1 class="w3-text-primary">Ostlandhalle</h1><?php
$spielplan->spiele = $ostlandhalle_spiele;
include '../../templates/spielplan/spielplan_spiele.tmp.php'; // Spiele
?><h1 class="w3-text-primary">Ballsporthalle</h1><?php
$spielplan->spiele = $ballsporthalle_spiele;
include '../../templates/spielplan/spielplan_spiele.tmp.php'; // Spiele

if ($spielplan->anzahl_teams != 3) {
    include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; // Abschlusstabelle
    include '../../templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
}

?>

<h1 class="w3-text-secondary">Halbfinale</h1>
<span class="w3-text-grey w3-margin-top">Spielzeit: 2 x 12&nbsp;min | Puffer: 6&nbsp;min</span>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-centered">
        <tr class="w3-primary">
            <th class="w3-hide-small"><?= Html::icon("schedule") ?><br>Zeit</th>    
            <th><?= Html::icon("sports") ?><br>Schiri</th>
            <th class="w3-hide-small"><?= Html::icon("sports_hockey") ?><br>Spiele</th>
            <th class="w3-hide-large w3-hide-medium"><?= Html::icon("sports_hockey") ?><br>Spiele</th>
        </tr>
        <?php foreach ($halbfinale as $spiel_id => $spiel): ?>
            <tr class="w3-light-grey">
                <td class="w3-hide-medium w3-hide-large"><b><?= $spiel["zeit"] ?></b></td>
                <td class="w3-hide-small" colspan="3" style="white-space: nowrap;"><b><?= $spiel["name"] ?></b></td>
                <td class="w3-hide-medium w3-hide-large" colspan="2" style="white-space: nowrap;"><b><?= $spiel["name"] ?></b></td>
            </tr>
            <tr>
                <td class="w3-hide-small" style="white-space: nowrap;"><?= $spiel["zeit"] ?></td>
                <td style="white-space: nowrap;"><?= $spiel["schiri_a"] ?><br><?= $spiel["schiri_b"] ?></td>
                <td class="w3-hide-medium w3-hide-large" style="white-space: nowrap;"><?= $spiel["team_a"] ?><br><?= $spiel["team_b"] ?></td>
                <td class="w3-hide-small" style="white-space: nowrap;"><?= $spiel["team_a"] ?> - <?= $spiel["team_b"] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<h1 class="w3-text-secondary">Finale</h1>
<span class="w3-text-grey w3-margin-top">Spielzeit: 2 x 12&nbsp;min | Puffer: 6&nbsp;min</span>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-centered">
        <tr class="w3-primary">
            <th class="w3-hide-small"><?= Html::icon("schedule") ?><br>Zeit</th>    
            <th><?= Html::icon("sports") ?><br>Schiri</th>
            <th class="w3-hide-small"><?= Html::icon("sports_hockey") ?><br>Spiele</th>
            <th class="w3-hide-large w3-hide-medium"><?= Html::icon("sports_hockey") ?><br>Spiele</th>
        </tr>
        <?php foreach ($finale as $spiel_id => $spiel): ?>
            <tr class="w3-light-grey">
                <td class="w3-hide-medium w3-hide-large"><b><?= $spiel["zeit"] ?></b></td>
                <td class="w3-hide-small" colspan="3" style="white-space: nowrap;"><b><?= $spiel["name"] ?></b></td>
                <td class="w3-hide-medium w3-hide-large" colspan="2" style="white-space: nowrap;"><b><?= $spiel["name"] ?></b></td>
            </tr>
            <tr>
                <td class="w3-hide-small" style="white-space: nowrap;"><?= $spiel["zeit"] ?></td>
                <td style="white-space: nowrap;"><?= $spiel["schiri_a"] ?><br><?= $spiel["schiri_b"] ?></td>
                <td class="w3-hide-medium w3-hide-large" style="white-space: nowrap;"><?= $spiel["team_a"] ?><br><?= $spiel["team_b"] ?></td>
                <td class="w3-hide-small" style="white-space: nowrap;"><?= $spiel["team_a"] ?> - <?= $spiel["team_b"] ?></td>
            </tr>
        <?php endforeach; ?>
    </table>
</div>

<?php

include '../../templates/footer.tmp.php';
