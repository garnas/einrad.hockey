<?php
//Spielplan-Objekt aus Url-TurnierID erstellen
$turnier_id=$_GET['turnier_id'];
$spielplan = new Spielplan($turnier_id);
//Existiert das Turnier?
if(empty($spielplan->akt_turnier->daten)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location : ../public/turniere.php');
    die();
}
//Nur Relevant für Ligacenter oder Teamcenter
if (isset($ligacenter) or isset($teamcenter)){
    //Besteht die Berechtigung das Turnier zu bearbeiten? 
    if (($_SESSION['team_id'] ?? false) != $spielplan->akt_turnier->daten['ausrichter'] && !$ligacenter){
        Form::error("Nur der Ausrichter darf Spielergebnisse eintragen");
        header('Location: ../liga/spielplan.php?turnier_id=' . $turnier_id);
        die();
    }
    if ($teamcenter && Config::time_offset() - strtotime($spielplan->akt_turnier->daten['datum']) > 48*60*60){
        Form::error("Bitte wende dich an den Ligaausschuss um Ergebnisse nachträglich einzutragen.");
        header('Location: ../liga/spielplan.php?turnier_id=' . $turnier_id);
        die();
    }
}
//Ist das Turnier in der richtigen Phase?
if(!in_array($spielplan->akt_turnier->daten['phase'], array('ergebnis', 'spielplan'))){
    Form::error("Turnier befindet sich in der falschen Phase");
    header('Location: ../liga/turnier_details.php?turnier_id=' . $turnier_id);
    die();
}

//Hat das Turnier die richtige Anzahl an Teams?
if($spielplan->anzahl_teams < 4 or $spielplan->anzahl_teams > 7){
    Form::error("Falsche Anzahl an Teams für die Spielplanerstellung");
    header('Location: ../liga/turnier_details.php?turnier_id=' . $turnier_id);
    die();
}

//Spielplan erstellen / Spielplan aus der Datenbank lesen, falls schon mal einer erstellt worden ist.
$spielplan->create_spielplan_jgj();
$tabelle=$spielplan->get_turnier_tabelle();
$teamliste=$spielplan->teamliste;
$spielliste=$spielplan->get_spiele();
$spielzeit = $spielplan->getSpielzeiten();
//Penalty Anzeigen? //Für Spielplan/Druckanzeige
$penalty_anzeigen = false;
foreach($spielliste as $spiel){
    if (!is_null($spiel["penalty_b"]) or !is_null($spiel["penalty_a"])){
        $penalty_anzeigen = true;
    }
}