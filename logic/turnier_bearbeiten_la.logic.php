<?php
//Formularauswertung Turnier bearbeiten für Ligaausschuss

//Formularauswertung Turnier löschen
if (isset($_POST['delete_turnier'])) {
    if ($_POST['delete_turnier_check'] !== 'checked'){
        Form::error('Bitte Hinweistext vor dem Löschen des Turnieres lesen.');
    }else{
        $turnier->delete($_POST['delete_turnier_grund']);
        Form::affirm("Turnier wurde gelöscht");
        header('Location: ../ligacenter/lc_turnierliste.php#deleted');
        die();
    }
}

//Forumlarauswertung Turnierdaten ändern
if (isset($_POST['turnier_bearbeiten_la'])) {  
    $error = false;
    //Ausrichter setzen
    $ausrichter = Team::teamname_to_teamid($_POST['ausrichter']);
    if (!Team::is_ligateam($ausrichter)){
        $error = true;
        Form::error ("Ausrichter wurde nicht gefunden");
    }

    // Turnierblock ändern:
    $art = $_POST['art']; 
    if ($art == "III"){
        $tblock = $_POST['block'];
        $fixed = "Ja";
    }elseif($art == "spass"){
        $tblock = "spass";
        $fixed = "Ja";
    }elseif($art == "fixed"){
        $tblock = $_POST['block'];
        $fixed = "Ja";
    }elseif ($art == "final"){
        $tblock = "final";
        $fixed = "Ja";
    }elseif ($art == "I"){
        $fixed = "Nein";
        $tblock = $_POST['block'];
    }elseif ($art == "II"){
        $fixed = "Nein";
        $tblock = $_POST['block'];
    }else{
        $error = true;
        Form::error("Ungültiger Turnierblock");
    }
    // Restliche Daten:
    $datum = $_POST['datum'];
    $phase = $_POST['phase'];
    $tname = $_POST['tname'];
    if (empty($datum) or empty($phase)){
        Form::error('Datum und/oder Phase sind leer');
        $error = true;
    }

    // Ändern der Turnierdaten
    if (!$error){
        $turnier->change_turnier_liga($tname, $ausrichter, $art, $tblock, $fixed, $datum, $phase);
        Form::affirm("Turnierdaten wurden geändert");
        header('Location: ../liga/turnier_details.php?turnier_id=' . $turnier->details['turnier_id']);
        die();
    }else{
        Form::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}