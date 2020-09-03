<?php
//eingetragene Tore speichern falls vorher eingetragen
if(isset($_POST["gesendet_tur"])){
    for($i=0;$i<$spielplan->get_anzahl_spiele();$i++){
        $spielplan->update_spiel($i+1,$_POST["toreAPOST"][$i],$_POST["toreBPOST"][$i],$_POST["penAPOST"][$i],$_POST["penBPOST"][$i]);
    }
}

//Turnierergebnisse speichern
if(isset($_POST["gesendet_turnierergebnisse"])){
    //Sind alle spiele gespielt und kein Penalty mehr notwendig
    $spielplan->set_ergebnis($tabelle);
    header('Location: ' . db::escape($_SERVER['REQUEST_URI']));
    die();
}

$penalty_warning = $spielplan->penalty_warning;

//Hinweis Penalty
if (!empty($penalty_warning)){
    Form::attention($penalty_warning);
}

//Hinweis Kaderkontrolle und Turnierreport
$turnier_report = new TurnierReport($turnier_id);
if (!$turnier_report->kader_check()){
    Form::affirm("Bitte kontrolliert die Teamkader und setzt im " . Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, 'Turnierreport') . " das entsprechende Häckchen.");
}