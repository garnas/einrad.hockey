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
    1 => "Swiss Team",
    2 => "Deutschland 1",
    3 => "Deutschland 2",
    4 => "Swiss Team 2",
    5 => "Deutschland 3",
    6 => "Aussie Deutsch United",
    7 => "Deutschland 4",
    8 => "B&B"
];
$vorlage = "euhc_a";
$turnier_id = 1;
$startzeit = "10:00:00";
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

