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
$titel = "Spielplan Ligacenter";
$content = "Der Spielplan für das Einradhockey-Turnier in ". $spielplan->akt_turnier->daten['ort'] . "am" . date("d.m.Y", strtotime($spielplan->akt_turnier->daten['datum']));
include '../../templates/header.tmp.php';
include '../../templates/spielplan_vorTurnierTabelle.tmp.php'; //Teamliste
include '../../templates/spielplan_spieleTabelleForm_mobile.tmp.php'; //Spielplan
include '../../templates/spielplan_ergebnisTabelle_mobile.tmp.php'; //Turniertabelle
?>
<!-- Turnierergebnisse in die Datenbank eintragen -->
<form method="post">
    <p><input type="submit" name="gesendet_turnierergebnisse" class="w3-block w3-button w3-tertiary" value="Ergebnisse speichern"></p>
</form>
<?php
include '../../templates/footer.tmp.php';