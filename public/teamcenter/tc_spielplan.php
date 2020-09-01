<!-- 
TODO:
- Datum und Ort des Turniers in den "Spielplan" einfügen
- Title entsprechend den anderen anpassen
-->
<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth

$turnier_id=$_GET['turnier_id'];
$akt_turnier=new Turnier($turnier_id);
//Existiert das Turnier?
if(empty($akt_turnier->daten)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location : ../public/turniere.php');
    die();
}
//Ist das Turnier in der richtigen Phase?
if(!in_array($akt_turnier->daten['phase'], array('ergebnis', 'spielplan'))){
    Form::error("Turnier befindet sich in der falschen Phase");
    header('Location : ../public/turniere.php');
    die();
}
//Besteht die Berechtigung das Turnier zu bearbeiten? 
if ($_SESSION['team_id'] != $akt_turnier->daten['ausrichter'] && !$ligacenter){
    Form::error("Nur der Ausrichter darf Spielergebnisse eintragen");
    header('Location: ../liga/spielplan.php?turnier_id=' . $turnier_id);
    die();
}

$spielplan = new Spielplan($turnier_id);
$spielplan->create_spielplan_jgj();
//eingetragene Tore speichern falls vorher eingetragen
if(isset($_POST["gesendet_tur"])){
    for($i=0;$i<$spielplan->get_anzahl_spiele();$i++){
        //echo "in tc_spielplan -> reloaden Spiel:".$i." <br>";
        $spielplan->update_spiel($i+1,$_POST["toreAPOST"][$i],$_POST["toreBPOST"][$i],$_POST["penAPOST"][$i],$_POST["penBPOST"][$i]);
    }
}
$tabelle=$spielplan->get_turnier_tabelle();
$teamliste=$spielplan->teamliste;
$spielliste=$spielplan->get_spiele();
$penalty_warning=$spielplan->penalty_warning;
if(empty($penalty_warning)){
    $penalty_warning=" Kein Penalty notwendig";
}else{
    Form::attention($penalty_warning);
}
//Turnierergebnisse speichern
//TODO nach Datum testen, ist es später ale Turnier begin und nicht merh als X Tage nach Turneir??
if(isset($_POST["gesendet_turnierergebnisse"])){
    //Sind alle spiele gespielt und kein Penalty mehr notwendig
    $spielplan->set_ergebnis($tabelle);
}

//Hinweis Kaderkontrolle und Turnierreport
$turnier_report = new TurnierReport($turnier_id);
if (!$turnier_report->kader_check()){
    Form::affirm("Bitte kontrolliert die Teamkader und setzt im " . Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, 'Turnierreport') . " das entsprechende Häckchen.");
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Spielplan Teamcenter";
$content = "Der Spielplan für das Einradhockey-Turnier in ". $akt_turnier->daten['ort'] . "am" . date("d.m.Y", strtotime($akt_turnier->daten['datum']));
include '../../templates/header.tmp.php';
?>

<!-- LINK TURNIERREPORT -->
<p><?=Form::link('../teamcenter/turnier_report.php?turnier_id=' . $turnier_id, '<i class="material-icons">create</i> Turnierreport ausfüllen')?></p>

<!-- TEAMLISTE -->
<?php
include '../../templates/spielplan_vorTurnierTabelle.tmp.php';
?>

<!-- SPIELE -->
<h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
<form method="post">
    <?php
        include '../../templates/spielplan_spieleTabelleForm.tmp.php';
    ?>
     <p><input type="submit" name="gesendet_tur" class="w3-block w3-button w3-tertiary" value="Spielergebnisse zwischenspeichern"></p>
</form>
<p><b>Spielergebnisse sollten zusätzlich schriftlich festgehalten werden</b></p>
<!-- Penalty Warnung -->
<h3 class="w3-text-secondary w3-margin-top">Penalty</h3>
<p> <?= $penalty_warning?></p>

<!-- ABSCHLUSSTABELLE -->
<?php
    include '../../templates/spielplan_ergebnisTabelle.tmp.php';
?>
<form method="post">
    <?php if($teamcenter && $akt_turnier->daten['phase'] == 'ergebnis'){?>
        <p class="w3-text-green">Dem Ligaausschuss liegt ein Turnierergebniss vor. Durch erneutes Speichern kann das Turnierergebnis verändert werden.</p>
    <?php }//endif?>
    <p>
        <input type="submit" name="gesendet_turnierergebnisse" class="w3-block w3-button w3-tertiary" value="Ergebnisse speichern">
    </p>
</form>

<?php
include '../../templates/footer.tmp.php';