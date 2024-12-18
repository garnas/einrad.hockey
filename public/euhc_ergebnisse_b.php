<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../init.php';
// Turnier-ID
if ($_GET["euhc_key"] != Env::EUHC_KEY) {
    die("Falscher link");
}

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
$vorlage = "euhc_b";
$turnier_id = 0;
$startzeit = "12:30:00";
require_once Env::BASE_PATH . '/logic/spielplan_euhc.logic.php';

//require_once '../logic/spielplan_form.logic.php'; //Wertet Formular aus

if (isset($_POST["tore_speichern"])) {
    // Neu eingetragene Tore speichern
    foreach ($spielplan->spiele as $spiel_id => $spiel) {
        if (
            (string) $spiel['tore_a'] === ($_POST["tore_a"][$spiel_id] ?? '')
            && (string) $spiel['tore_b'] === ($_POST["tore_b"][$spiel_id] ?? '')
            && (string) $spiel['penalty_a'] === ($_POST["penalty_a"][$spiel_id] ?? '')
            && (string) $spiel['penalty_b'] === ($_POST["penalty_b"][$spiel_id] ?? '')
        ) {
            continue;
        }
        $spielplan->set_tore(
            $spiel['spiel_id'],
            $_POST["tore_a"][$spiel_id] ?? '',
            $_POST["tore_b"][$spiel_id] ?? '',
            $_POST["penalty_a"][$spiel_id] ?? '',
            $_POST["penalty_b"][$spiel_id] ?? ''
        );
        $spiel['tore_a'] = $_POST["tore_a"][$spiel_id]  ?? '';
        $spiel['tore_b'] = $_POST["tore_b"][$spiel_id]  ?? '';
        $spiel['penalty_a'] = $_POST["penalty_a"][$spiel_id]  ?? '';
        $spiel['penalty_b'] = $_POST["penalty_b"][$spiel_id]  ?? '';
        Discord::tickerUpdate($spiel);
    }

    Html::info('Spielergebnisse wurden gespeichert');
    header('Location: ' . db::escape($_SERVER['REQUEST_URI']));
    die();
}

if(!$spielplan->validate_penalty_ergebnisse()){
    Html::error("Achtung: Es liegen falsch eingetragene Penaltyergebnisse vor.");
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "EUHC Spielplan";


include Env::BASE_PATH . '/templates/header.tmp.php';
include Env::BASE_PATH . '/templates/spielplan/spielplan_tore_eintragen.tmp.php'; // Spielplan -> Formular übertragen
include Env::BASE_PATH . '/templates/spielplan/spielplan_turniertabelle.tmp.php'; // Turniertabelle
include Env::BASE_PATH . '/templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Turniertabelle
include Env::BASE_PATH . '/templates/footer.tmp.php';

