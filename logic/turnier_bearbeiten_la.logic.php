<?php
//Formularauswertung Turnier bearbeiten für Ligaausschuss

//Formularauswertung Turnier löschen
if (isset($_POST['delete_turnier'])) {
    if ($_POST['delete_turnier_check'] !== 'checked'){
        Form::error('Bitte Hinweistext vor dem Löschen des Turnieres lesen.');
    }else{
        $turnier->log("Turnier wurde gelöscht", "Ligaausschuss");
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
    if (isset($_POST['ausrichter'])){
        $ausrichter = Team::teamname_to_teamid($_POST['ausrichter']);
        if (empty($ausrichter)){
            $error = true;
            Form::error ("Ausrichter wurde nicht gefunden");
        }
    }
    //Turnierblock ändern:
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
    //Restliche Daten:
    $datum = $_POST['datum'];
    $phase = $_POST['phase'];
    $tname = $_POST['tname'];
    if (empty($datum) or empty($phase)){
        Form::error('Datum und/oder Phase sind leer');
        $error = true;
    }

    //Ändern der Turnierdaten
    if (!$error){
        if ($turnier->change_turnier_liga($tname, $ausrichter, $art, $tblock, $fixed, $datum, $phase)){
            Form::affirm("Turnierdaten wurden geändert");
            if ($turnier->details['datum'] != $datum){
                LigaBot::set_spieltage(); //Spieltage ändern sich eventuell, je nach Datumsveränderung
                $turnier->log("Datum: " . $turnier->details['datum'] ." -> ". $datum, "Ligaausschuss");
            }
            if ($turnier->details['tname'] != $tname){
                $turnier->log("Turniername: " . $turnier->details['tname'] ." -> ". $tname, "Ligaausschuss");
            }
            if ($turnier->details['ausrichter'] != $ausrichter){
                $turnier->log("Ausrichter: " . $turnier->details['teamname'] ." -> ". Team::teamid_to_teamname($ausrichter), "Ligaausschuss");
            }
            if ($turnier->details['art'] != $art){
                $turnier->log("Art: " . $turnier->details['art'] ." -> ". $art, "Ligaausschuss");
            }
            if ($turnier->details['tblock'] != $tblock){
                $turnier->log("Turnierblock: " . $turnier->details['tblock'] ." -> ". $tblock, "Ligaausschuss");
            }
            if ($turnier->details['tblock_fixed'] != $fixed){
                $turnier->log("Fixiert: " . $turnier->details['tblock_fixed'] ." -> ". $fixed, "Ligaausschuss");
            }
            if ($turnier->details['phase'] != $phase){
                $turnier->log("Phase: " . $turnier->details['phase'] ." -> ". $phase, "Ligaausschuss");
            }
            header('Location: ../liga/turnier_details.php?turnier_id=' . $turnier->details['turnier_id']);
            die();
        }else{
            $turnier->log("Fehler beim Schreiben der Datenbank", "Ligaausschuss");
        }
    }else{
        Form::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}