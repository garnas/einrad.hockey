<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation
require_once '../../logic/spielplan_form.logic.php'; //Wertet Formular aus

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Ergebnisse eintragen | Teamcenter";
Html::$content = "Der Spielplan für das Einradhockey-Turnier in ". $spielplan->turnier->get_ort() . " am " . date("d.m.Y", strtotime($spielplan->turnier->get_datum()));
include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_titel.tmp.php'; // Titel
?>

<h3>Bitte ausfüllen</h3>
<p>
    <a href="<?=Env::BASE_URL?>/teamcenter/tc_turnier_report.php?turnier_id=<?= $spielplan->turnier_id ?>"
       class="w3-button w3-tertiary w3-block"
    >
            <i class="material-icons">report</i> Turnierreport + Teamkader
    </a>
</p>

<?php
include '../../templates/spielplan/spielplan_tore_eintragen.tmp.php'; // Spielplan -> Formular übertragen
include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; // Turniertabelle
include '../../templates/spielplan/spielplan_ergebnis_senden.tmp.php'; // Ergebnis senden
include '../../templates/footer.tmp.php';