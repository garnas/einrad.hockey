<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/spielplan.logic.php'; //Erstellt Spielplanobjekt nach Validation
require_once '../../logic/spielplan_form.logic.php'; //Wertet Formular aus

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Ergebnisse eintragen | Teamcenter";
$content = "Der Spielplan für das Einradhockey-Turnier in ". $akt_turnier->daten['ort'] . "am" . date("d.m.Y", strtotime($akt_turnier->daten['datum']));
include '../../templates/header.tmp.php';
include '../../templates/spielplan_vorTurnierTabelle.tmp.php'; //Teamliste;
include '../../templates/spielplan_spieleTabelleForm_mobil.tmp.php'; //Spielplan
?>

<!-- Penalty Warnung -->
<h3 class="w3-text-secondary w3-margin-top">Penalty</h3>
<p> <?= $penalty_warning?></p>

<!-- ABSCHLUSSTABELLE -->
<?php
    include '../../templates/spielplan_ergebnisTabelle_mobil.tmp.php';
?>
<form method="post">
    <?php if($akt_turnier->daten['phase'] == 'ergebnis'){?>
        <p class="w3-text-green">Dem Ligaausschuss liegt ein Turnierergebniss vor. Durch erneutes Speichern kann das Turnierergebnis verändert werden.</p>
    <?php }//endif?>
    <?php if($akt_turnier->daten['phase'] != 'ergebnis'){?>
        <p class="w3-text-grey">Dem Ligaausschuss liegt noch kein Turnierergebniss vor.</p>
    <?php }//endif?>
    <p>
        <input type="submit" name="gesendet_turnierergebnisse" class="w3-block w3-button w3-tertiary" value="Ergebnisse speichern">
    </p>
</form>

<?php
include '../../templates/footer.tmp.php';