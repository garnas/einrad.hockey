<?php
//Formularauswertung Turnier bearbeiten für Ligaausschuss

//Formularauswertung Turnier löschen
if (isset($_POST['delete_turnier'])) {
    if ($_POST['delete_turnier_check'] !== 'checked'){
        Html::error('Bitte Hinweistext vor dem Löschen des Turnieres lesen.');
    }else{
        $turnier->delete($_POST['delete_turnier_grund']);
        Html::info("Turnier wurde gelöscht");
        header('Location: ../ligacenter/lc_turnierliste.php#deleted');
        die();
    }
}

//Forumlarauswertung Turnierdaten ändern
if (isset($_POST['turnier_bearbeiten_la'])) {  
    $error = false;
    //Ausrichter setzen
    $ausrichter = Team::name_to_id($_POST['ausrichter']);
    if (!Team::is_ligateam($ausrichter)){
        $error = true;
        Html::error ("Ausrichter wurde nicht gefunden");
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
        Html::error("Ungültiger Turnierblock");
    }
    // Restliche Daten:
    $datum = $_POST['datum'];
    $phase = $_POST['phase'];
    $tname = $_POST['tname'];
    if (empty($datum) || empty($phase)){
        Html::error('Datum und/oder Phase sind leer');
        $error = true;
    }

    // Ändern der Turnierdaten
    if (!$error){
        $turnier->change_turnier_liga($tname, $ausrichter, $art, $tblock, $fixed, $datum, $phase);
        Html::info("Turnierdaten wurden geändert");
        header('Location: ../liga/turnier_details.php?turnier_id=' . $turnier->details['turnier_id']);
        die();
    }else{
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}