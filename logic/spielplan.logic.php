<?php
//Spielplan-Objekt aus Url-TurnierID erstellen
$turnier_id = $_GET['turnier_id'];

$spielplan = new Spielplan((new Turnier ($turnier_id)));

// Gibt es einen Spielplan zu diesem Turnier?
if(empty($spielplan->tore_tabelle)){
    Form::error("Spielplan wurde nicht gefunden");
    header('Location: ../liga/turniere.php');
    die();
}
$spiele = $spielplan->get_spiele();
#db::debug($spiele);

$spielplan->direkter_vergleich($spielplan->tore_tabelle);
$spielplan->set_wertigkeiten();


//Nur Relevant für Ligacenter oder Teamcenter
if ($ligacenter or $teamcenter){
    //Besteht die Berechtigung das Turnier zu bearbeiten?
    if (($_SESSION['team_id'] ?? false) != $spielplan->turnier->details['ausrichter'] && !$ligacenter){
        Form::error("Nur der Ausrichter kann Spielergebnisse eintragen");
        header('Location: ../liga/spielplan.php?turnier_id=' . $turnier_id);
        die();
    }
    if ($teamcenter && Config::time_offset() - strtotime($spielplan->turnier->details['datum']) > 48*60*60){
        Form::error("Bitte wende dich an den Ligaausschuss um Ergebnisse nachträglich zu verändern.");
        header('Location: ../liga/spielplan.php?turnier_id=' . $turnier_id);
        die();
    }
}

$spielplan->details['dauer'] = $spielplan->details['anzahl_halbzeiten'] * $spielplan->details['halbzeit_laenge']
    + $spielplan->details['pause'];
#db::debug($spielplan->platzierungstabelle);
#db::debug($spielplan->penalty_begegnungen);
#db::debug($spielplan->penalty_warnung);

// Pixellänge des längsten Teamnamens für die perfekte Darstellung des Spielplans
$function = function ($platz){
    return strlen($platz['teamname']);
};
$teamnamen_laengen = array_map(function ($platz){return strlen($platz['teamname']);},$spielplan->platzierungstabelle);
$width_in_px = max($teamnamen_laengen) * 8; // 7.5 Durchschnittliche px-Weite eines Characters

// Penalty Anzeigen? //Für Spielplan/Druckanzeige
$penalty_anzeigen = false;
foreach($spiele as $index => $spiel){
    if (!is_null($spiel["penalty_b"]) or !is_null($spiel["penalty_a"])){
        $penalty_anzeigen = true;
    }
}