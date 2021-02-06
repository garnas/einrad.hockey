<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php';//Auth
require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation
require_once '../../logic/spielplan_form.logic.php'; //Wertet Formular aus

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Ergebnisse eintragen | Ligacenter";
$content = "Der Spielplan für das Einradhockey-Turnier in ". $spielplan->turnier->details['ort'] . " am " . date("d.m.Y", strtotime($spielplan->turnier->details['datum']));
include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_titel.tmp.php'; // Titel
?>

    <p>
        <a href="<?=CONFIG::BASE_LINK?>/ligacenter/lc_turnier_report.php?turnier_id=<?= $spielplan->turnier_id ?>"
           class="w3-button w3-tertiary w3-block"
        >
            <i class="material-icons">report</i> Turnierreport + Teamkader
        </a>
    </p>

<?php
include '../../templates/spielplan/spielplan_tore_eintragen.tmp.php'; // Spielplan -> Formular übertragen
include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; // Turniertabelle
include '../../templates/spielplan/spielplan_ergebnis_senden.tmp.php'; // Ergebnis senden
include '../../templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Turniertabelle
include '../../templates/footer.tmp.php';