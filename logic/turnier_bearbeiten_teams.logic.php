<?php
//Kann das Turnier erweitert werden?
if ($akt_turnier->daten['phase'] == 'melde' && strlen($akt_turnier->daten['tblock']) < 3 && $akt_turnier->daten['tblock'] != 'AB' && ($akt_turnier->daten['art'] == 'I' or $akt_turnier->daten['art'] == 'II')){
    $blockhoch = true;
}else{
    $blockhoch = false;
}
//Kann das Turnier auf ABCDEF erweitert werden?
if ($akt_turnier->daten['phase'] == 'melde' && $akt_turnier->daten['art'] != 'III' && ($akt_turnier->daten['art'] == 'I' or $akt_turnier->daten['art'] == 'II')){
    $blockfrei = true;
}else{
    $blockfrei = false;
}

//Formularauswertung
if (isset($_POST['change_turnier'])) {

    $error = false;
    $hallenname = $_POST['hallenname'];
    $strasse = $_POST['strasse'];
    $plz = $_POST['plz'];
    $ort = $_POST['ort'];
    $haltestellen = $_POST['haltestellen'];
    $hinweis = $_POST['hinweis'];
    $startgebuehr = $_POST['startgebuehr'];
    $organisator = $_POST['organisator'];
    $handy = $_POST['handy'];
    $startzeit = $_POST['startzeit'];
    $plaetze = $_POST['plaetze'];
    //Leere Felder können eigentlich nicht auftreten (nur durch html-Manipulation), aber sicherheitshalber dass hier...
    if (empty($plaetze) or empty($startzeit) or empty($hallenname) or empty($strasse) or empty($plz) or empty($ort) or empty($hinweis) or empty($organisator) or empty($handy)){
        $error = true;
        Form::error("Bitte alle nicht optionalen Felder ausfüllen.");
    }
    
    //Besprechung
    if ($_POST['besprechung'] == 'Ja'){
        $besprechung = 'Ja';
    }else{
        $besprechung = 'Nein';
    }

    //Validierung Startzeit:
    if ($startzeit != $akt_turnier->daten['startzeit'] && $akt_turnier->daten['art'] == 'final' && $teamcenter){
        $error = true;
        Form::error("Die Startzeit bei Abschlussturnieren kann nur vom Ligaausschuss geändert werden.");
    }
    if ((date("H", strtotime($startzeit)) < 9 or date("H", strtotime($startzeit)) > 14) && $teamcenter){
        $error = true;
        Form::error("Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen spätestens um 20:00&nbsp;Uhr beendet sein");
    }

    //Anzahl der Plätze bzw ob 8er DKO- oder Gruppen-Spielplan
    if ($plaetze == '8 dko'){
        $plaetze = 8;
        $spielplan = 'dko';
    }elseif ($plaetze == '8 gruppen'){
        $plaetze = 8;
        $spielplan = 'gruppen';
    }else{
        $spielplan = 'jgj';
    }

    //Validierung der Plätze
    if ($akt_turnier->daten['art'] == 'final' && $plaetze != $akt_turnier->daten['plaetze'] && $teamcenter){ //Anzahl der Plätze darf nur geändert werden, wenn es sich nicht um ein Finalturnier handelt
        Form::error("Das Ändern der Anzahl der Plätze ist bei Abschlussturnieren können nur vom Ligaausschuss geändert werden.");
        $error = true;
    }
    if ($plaetze < 4 or $plaetze > 8){
        $error = true;
        Form::error("Ungültige Anzahl an Turnierplätzen");
    }
    //4er Turniere nur über den LA
    if ($plaetze == 4 && $teamcenter){
        $error = true;
        Form::error("Ungültige Anzahl an Turnierplätzen");
    }

    /////////////////////////////////////////////////////////////////

    //Keine Änderung der Plätze in der Spielplanphase
    if ($akt_turnier->daten['phase'] == 'spielplan'){
        if ($akt_turnier->daten['plaetze'] != $plaetze && $teamcenter){
            $error = true;
            Form::error("Die Anzahl der Plätze kann in der Spielplanphase nicht mehr geändert werden. Bitte wende dich unter ".Config::LAMAIL." an den Ligaaussschuss.");
        }
    }
    //Keine Änderung der Plätze in der Spielplanphase
    if ($akt_turnier->daten['phase'] == 'ergebnis' && $teamcenter){
        $error = true;
        Form::error("Turniere können in der Ergebnisphase nicht mehr geändert werden. Bitte wende dich unter ".Config::LAMAIL." an den Ligaaussschuss.");
    }
    //////////////////Block erweitern//////////////////

    //Es wurden beide Häckchen gesetzt
    if (isset($_POST['block_frei']) && isset($_POST['block_erweitern'])){
        $error = true;
        Form::error ("Bitte entweder um den nächsthöheren Block oder auf ABCDEF öffnen");
    }
    
    $tblock = $akt_turnier->daten['tblock'];
    $fixed = $akt_turnier->daten['tblock_fixed'];
    $art = $akt_turnier->daten['art'];
    $erweitern = false; //Wird auf true gesetzt, wenn der Turnierblock erweitert werden soll
    
    //Um den nächst höheren Buchstaben erweitern
    if (isset($_POST['block_erweitern'])){
        if ($blockhoch){
            $chosen = array_search($akt_turnier->daten['tblock'], Config::BLOCK_ALL);
            if (($_POST['block_erweitern'] ?? '') == Config::BLOCK_ALL[$chosen-1]){
                $tblock = Config::BLOCK_ALL[$chosen-1];
                $fixed = $akt_turnier->daten['tblock_fixed'];
                $erweitern = true;
            }
        }else{
            $error = true;
            Form::error ("Das Turnier kann nicht um den nächsthöheren Block erweitert werden.");
        }
    }

    //Auf ABCDEF erweitern
    if (isset($_POST['block_frei'])){
        if ($blockfrei){
            if (($_POST['block_frei'] ?? '') == 'ABCDEF'){
                $tblock = 'ABCDEF';
                $fixed = 'Ja';
                $art = 'III';
                $erweitern = true;
            }
        }else{
            $error = true;
            Form::error ("Das Turnier kann nicht auf ABCDEF erweitert werden.");
        }
    }

    //Ändern der Turnierdaten
    if (!$error){
        //Autor der Turnierlogs festlegen
        if ($teamcenter == true){ //sollte übergeben werden
            $autor = $_SESSION['teamname'];
        }elseif ($ligacenter == true){
            $autor = "Ligaausschuss";
        }
        //Turnierblock erweitern
        if ($erweitern){
            $akt_turnier->change_turnier_block($tblock, $fixed, $art);
            $akt_turnier->daten['tblock'] = $tblock;
            $akt_turnier->spieleliste_auffuellen(); //Spielen-Liste auffuellen
            $akt_turnier->schreibe_log("Turnierblock geändert zu $tblock", $autor);
            Form::affirm("Turnier wurde erweitert");
            if ($akt_turnier->daten['tblock_fixed'] != $fixed){
                $akt_turnier->schreibe_log("Turnierfixierung geändert zu $fixed", $autor);
            }
            if ($akt_turnier->daten['art'] != $art){
                $akt_turnier->schreibe_log("Turnierart geändert zu $art", $autor);
            }
        }
        //Ändern der Turnierdetails
        if ($akt_turnier->change_turnier_details($startzeit, $besprechung, $plaetze, $spielplan, $hallenname, $strasse, $plz, $ort, $haltestellen, $hinweis, $startgebuehr, $organisator, $handy)){
            if ($akt_turnier->daten['startzeit'] != $startzeit){
                $akt_turnier->schreibe_log("Startzeit: " . $akt_turnier->daten['startzeit'] . " -> " . $startzeit, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['plaetze'] != $plaetze){
                $akt_turnier->schreibe_log("Plätze: " . $akt_turnier->daten['plaetze'] . " -> " . $plaetze, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['besprechung'] != $besprechung){
                $akt_turnier->schreibe_log("Besprechung: " . $akt_turnier->daten['besprechung'] . " -> " . $besprechung, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['hinweis'] != $hinweis){
                $akt_turnier->schreibe_log("Hinweis: " . $akt_turnier->daten['hinweis'] . " -> " . $hinweis, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['hallenname'] != $hallenname){
                $akt_turnier->schreibe_log("Hallenname: " . $akt_turnier->daten['hallenname'] . " -> " . $hallenname, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['plz'] != $plz){
                $akt_turnier->schreibe_log("PLZ: " . $akt_turnier->daten['plz'] . " -> " . $plz, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['strasse'] != $strasse){
                $akt_turnier->schreibe_log("Straße: " . $akt_turnier->daten['strasse'] . " -> " . $strasse, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['ort'] != $ort){
                $akt_turnier->schreibe_log("Ort: " . $akt_turnier->daten['ort'] . " -> " . $ort, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['haltestellen'] != $haltestellen){
                $akt_turnier->schreibe_log("Haltestellen: " . $akt_turnier->daten['haltestellen'] . " -> " . $haltestellen, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['organisator'] != $organisator){
                $akt_turnier->schreibe_log("Organisator: " . $akt_turnier->daten['organisator'] . " -> " . $startzeit, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['handy'] != $handy){
                $akt_turnier->schreibe_log("Handy: " . $akt_turnier->daten['handy'] . " -> " . $handy, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['startgebuehr'] != $startgebuehr){
                $akt_turnier->schreibe_log("Startgebühr: " . $akt_turnier->daten['startgebuehr'] . " -> " . $startgebuehr, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['spielplan'] != $spielplan){
                $akt_turnier->schreibe_log("Spielplan: " . $akt_turnier->daten['spielplan'] . " -> " . $spielplan, $autor);
                $mail = true;
            }
            if ($akt_turnier->daten['hallenname'] != $hallenname){
                $akt_turnier->schreibe_log("Hallenname: " . $akt_turnier->daten['hallenname'] . " -> " . $hallenname, $autor);
                $mail = true;
            }
        }
        if ($mail ?? false){
            MailBot::mail_turnierdaten_geaendert($akt_turnier);
            Form::affirm("Turnierdaten wurden geändert");
            header ('Location: ../liga/turnier_details.php?turnier_id=' . $akt_turnier->daten['turnier_id']);
            die();
        }else{
            Form::attention("Es wurden keine Daten geändert");
        }
    }else{
        Form::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}