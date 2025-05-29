<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';


$turnier = nTurnier::get(1239);
// Gibt es einen Spielplan zu diesem Turnier?
if (!Spielplan::check_exist(1239)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}
$spielplan = new Spielplan_JgJ($turnier);

$map =
    [
        1 => "12:00",
        2 => "12:00",
        3 => "12:24",
        4 => "12:24",
        5 => "12:48",
        6 => "12:48",
        7 => "13:12",
        8 => "13:12",
        9 => "13:36",
        10 => "13:36",
        11 => "14:00",
        12 => "14:00",
        13 => "14:24",
        14 => "14:24",
        15 => "14:48",
        16 => "14:48",
        17 => "15:12",
        18 => "15:12",
        19 => "15:36",
        20 => "15:36",
        21 => "16:00",
        22 => "16:00",
        23 => "16:24",
        24 => "16:24",
        25 => "16:48",
        26 => "16:48",
        27 => "17:12",
        28 => "17:12",
        29 => "17:36",
        30 => "17:36",
        31 => "18:00",
        32 => "19:30",
        33 => "19:55",
        34 => "20:20",
        35 => "20:45",
        36 => "21:10",
        37 => "21:35",
        38 => "22:00",
        39 => "22:25",
        40 => "22:50",
        41 => "23:15",
        42 => "23:40",
        43 => "00:05",
        44 => "00:30",
        45 => "00:55",
        46 => "01:20",
        47 => "01:45",
        48 => "02:10",
        49 => "02:35",
        50 => "03:00",
        51 => "03:25",
        52 => "03:50",
        53 => "04:15",
        54 => "04:40",
        55 => "05:05"
    ];

foreach ($spielplan->spiele as $id => $spiel) {
    $spielplan->spiele[$id]["zeit"] = $map[$id];
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Spielplan | Einradhockey";
Html::$content = "Der Spielplan für das Einradhockey-Turnier in " . $spielplan->turnier->get_ort()
    . " am " . date("d.m.Y", strtotime($spielplan->turnier->get_datum()));

include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_titel.tmp.php';
include '../../templates/spielplan/spielplan_teamliste.tmp.php'; // Teamliste
include '../../templates/spielplan/spielplan_spiele.tmp.php'; // Spiele
if ($spielplan->anzahl_teams != 3) {
    include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; // Abschlusstabelle
    include '../../templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Direkter Vergleich Tabellen
}
include '../../templates/footer.tmp.php';
