<!-- 
TODO:
- Datum und Ort des Turniers in den "Spielplan" einfügen
- Title entsprechend den anderen anpassen
- Formelemente auslesen
- Tabelle dynamisch erstellen
-->
<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php';//Auth

$turnier_id = 920;
//$turnier_id=$_GET['turnier_id'];
$akt_turnier=new Turnier($turnier_id);
//Existiert das Turneir??
if(empty($akt_turnier->daten)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location: ../ligacenter/lc_start.php');
    die();
}

$spielplan = new Spielplan($turnier_id);
$spielplan->create_spielplan_jgj();
//einegtragene Tore speichern falls vorher eingetragen
if(isset($_POST["gesendet_tur"])||isset($_POST["gesendet_turnierergebnisse"])){
    for($i=0;$i<$spielplan->get_anzahl_spiele();$i++){
        echo "in tc_spielplan -> reloaden Spiel:".$i." <br>";
        $spielplan->update_spiel($i+1,$_POST["toreAPOST"][$i],$_POST["toreBPOST"][$i],$_POST["penAPOST"][$i],$_POST["penBPOST"][$i]);
    }
}
$tabelle=$spielplan->get_turnier_tabelle();
$teamliste=$spielplan->teamliste;
$spielliste=$spielplan->get_spiele();
$penalty_warning=$spielplan->penalty_warning;
if(empty($penalty_warning)){
    $penalty_warning=" Kein Penalty notwendig";
}
//Turnierergebnisse speichern
if(isset($_POST["gesendet_turnierergebnisse"])){
    //Sind alle spiele gespielt und kein Penalty mehr notwendig
    $spielplan->set_ergebnis($tabelle);
}
$ort=$spielplan->ort;
$datum=$spielplan->datum;
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Spielplan Ligacenter";

include '../../templates/header.tmp.php';
?>
<div class="">
<h1 class="w3-text-primary w3-border-primary">Spielplan <?=$ort?>, <?=$datum?></h1>

<!-- TEAMLISTE -->
<?php
include '../../templates/spielplan_vorTurnierTabelle.tmp.php';
?>
<!-- SPIELE -->
<h3 class="w3-text-secondary w3-margin-top">Spiele</h3>
<form action ="lc_spielplan.php" method="post">
<?php
include '../../templates/spielplan_spieleTabelleForm.tmp.php';
?>

<!-- Penalty Warnung -->
<h3 class="w3-text-secondary w3-margin-top">Penalty</h3>
<p> <?= $penalty_warning?></p>
<!-- ABSCHLUSSTABELLE -->
<?php
include '../../templates/spielplan_ergebnisTabelle.tmp.php';
?>
</div>
<form action ="lc_spielplan.php" method="post">
<p><input type="submit" name="gesendet_turnierergebnisse" class="w3-block w3-button w3-tertiary" value="Ergebnisse speichern"></p>
</form>
<?php
include '../../templates/footer.tmp.php';