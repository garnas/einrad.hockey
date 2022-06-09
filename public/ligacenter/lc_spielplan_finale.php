<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_la.logic.php';//Auth

$turnier_id = (int) @$_GET['turnier_id'];
$turnier = nTurnier::get($turnier_id);
$spielplan = (new Spielplan_Final($turnier))->get_spielplan();

require_once '../../logic/spielplan_form.logic.php'; //Wertet Formular aus

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Ergebnisse eintragen | Ligacenter";
Html::$content = "Der Spielplan für das Einradhockey-Turnier in ". $spielplan->turnier->get_ort() . " am " . date("d.m.Y", strtotime($spielplan->turnier->get_datum()));
include '../../templates/header.tmp.php';
include '../../templates/spielplan/spielplan_titel.tmp.php'; // Titel
?>

    <p>
        <a href="<?=Env::BASE_URL?>/ligacenter/lc_turnier_report.php?turnier_id=<?= $spielplan->turnier_id ?>"
           class="w3-button w3-tertiary w3-block"
        >
            <i class="material-icons">report</i> Turnierreport + Teamkader
        </a>
    </p>

<?php
if (Env::ACTIVE_FINAL_DISCORD) {
    include include '../../templates/spielplan/spielplan_discord_write.tmp.php';
}
include '../../templates/spielplan/spielplan_tore_eintragen.tmp.php'; // Spielplan -> Formular übertragen
include '../../templates/spielplan/spielplan_turniertabelle.tmp.php'; // Turniertabelle
include '../../templates/spielplan/spielplan_ergebnis_senden.tmp.php'; // Ergebnis senden
include '../../templates/spielplan/spielplan_direkter_vergleich.tmp.php'; // Turniertabelle
include '../../templates/footer.tmp.php';