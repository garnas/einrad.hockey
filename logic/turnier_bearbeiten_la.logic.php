<?php
//Formularauswertung Turnier bearbeiten für Ligaausschuss

//Formularauswertung Turnier löschen
if (isset($_POST['delete_turnier'])) {
    if ($_POST['delete_turnier_check'] !== 'checked'){
        Form::error('Bitte Hinweistext vor dem Löschen des Turnieres lesen.');
    }else{
        $akt_turnier->schreibe_log("Turnier wurde gelöscht", "Ligaausschuss");
        $akt_turnier->delete($_POST['delete_turnier_grund']);
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
        if ($akt_turnier->change_turnier_liga($tname, $ausrichter, $art, $tblock, $fixed, $datum, $phase)){
            Form::affirm("Turnierdaten wurden geändert");
            if ($daten['datum'] != $datum){
                LigaBot::set_spieltage(); //Spieltage ändern sich eventuell, je nach Datumsveränderung
                $akt_turnier->schreibe_log("Datum: " . $daten['datum'] ." -> ". $datum, "Ligaausschuss", "Ligaausschuss"); 
            }
            if ($daten['tname'] != $tname){
                $akt_turnier->schreibe_log("Turniername: " . $daten['tname'] ." -> ". $tname, "Ligaausschuss");
            }
            if ($daten['ausrichter'] != $ausrichter){
                $akt_turnier->schreibe_log("Ausrichter: " . $daten['teamname'] ." -> ". Team::teamid_to_teamname($ausrichter), "Ligaausschuss");
            }
            if ($daten['art'] != $art){
                $akt_turnier->schreibe_log("Art: " . $daten['art'] ." -> ". $art, "Ligaausschuss");
            }
            if ($daten['tblock'] != $tblock){
                $akt_turnier->schreibe_log("Turnierblock: " . $daten['tblock'] ." -> ". $tblock, "Ligaausschuss");
            }
            if ($daten['tblock_fixed'] != $fixed){
                $akt_turnier->schreibe_log("Fixiert: " . $daten['tblock_fixed'] ." -> ". $fixed, "Ligaausschuss");
            }
            if ($daten['phase'] != $phase){
                $akt_turnier->schreibe_log("Phase: " . $daten['phase'] ." -> ". $phase, "Ligaausschuss");
            }
            header('Location: ../liga/turnier_details.php?turnier_id=' . $daten['turnier_id']);
            die();
        }else{
            $akt_turnier->schreibe_log("Fehler beim Schreiben der Datenbank", "Ligaausschuss");
        }
    }else{
        Form::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}