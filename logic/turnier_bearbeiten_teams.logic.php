<?php
//Kann das Turnier erweitert werden?
if ($akt_turnier->details['phase'] == 'melde' && strlen($akt_turnier->details['tblock']) < 3 && $akt_turnier->details['tblock'] != 'AB' && $akt_turnier->details['tblock'] != 'A' && ($akt_turnier->details['art'] == 'I' or $akt_turnier->details['art'] == 'II')){
    $blockhoch = true;
}else{
    $blockhoch = false;
}
//Kann das Turnier auf ABCDEF erweitert werden?
if ($akt_turnier->details['phase'] == 'melde' && $akt_turnier->details['art'] != 'III' && ($akt_turnier->details['art'] == 'I' or $akt_turnier->details['art'] == 'II')){
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
    
    //Besprechung
    if (($_POST['besprechung'] ?? '') == 'Ja'){
        $besprechung = 'Ja';
    }else{
        $besprechung = 'Nein';
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

    //Leere Felder können eigentlich nicht auftreten (nur durch html-Manipulation), aber sicherheitshalber dass hier...
    if (empty($plaetze) or empty($startzeit) or empty($hallenname) or empty($strasse) or empty($plz) or empty($ort) or empty($hinweis) or empty($organisator) or empty($handy)){
        $error = true;
        Form::error("Bitte alle nicht optionalen Felder ausfüllen.");
    }

    //Validierung Startzeit:
    if ($startzeit != $akt_turnier->details['startzeit']  && $teamcenter){
        if ($akt_turnier->details['art'] == 'final'){
            $error = true;
            Form::error("Die Startzeit bei Abschlussturnieren kann nur vom Ligaausschuss geändert werden.");
        }
        if ($startzeit != $daten['startzeit'] && (date("H", strtotime($startzeit)) < 9 or date("H", strtotime($startzeit)) > 14) && $teamcenter){
            $error = true;
            Form::error("Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen spätestens um 20:00&nbsp;Uhr beendet sein");
        }
    }

    //Validierung der Plätze
    if ($plaetze != $akt_turnier->details['plaetze']  && $teamcenter){
        if ($akt_turnier->details['art'] == 'final'){ //Anzahl der Plätze darf nur geändert werden, wenn es sich nicht um ein Finalturnier handelt
            Form::error("Das Ändern der Anzahl der Plätze ist bei Abschlussturnieren können nur vom Ligaausschuss geändert werden.");
            $error = true;
        }
        if ($plaetze < 5 or $plaetze > 8){
            $error = true; //4er nur via Ligaausschuss
            Form::error("Ungültige Anzahl an Turnierplätzen");
        }
    }
    
    /////////////////////////////////////////////////////////////////

    //Keine Änderung der Plätze in der Spielplanphase
    if ($akt_turnier->details['phase'] == 'spielplan'){
        if ($akt_turnier->details['plaetze'] != $plaetze && $teamcenter){
            $error = true;
            Form::error("Die Anzahl der Plätze kann in der Spielplanphase nicht mehr geändert werden. Bitte wende dich unter ".Config::LAMAIL." an den Ligaaussschuss.");
        }
    }
    //Keine Änderung der Plätze in der Spielplanphase
    if ($akt_turnier->details['phase'] == 'ergebnis' && $teamcenter){
        $error = true;
        Form::error("Turniere können in der Ergebnisphase nicht mehr geändert werden. Bitte wende dich unter ".Config::LAMAIL." an den Ligaaussschuss.");
    }
    //////////////////Block erweitern//////////////////

    //Es wurden beide Häkchen gesetzt
    if (isset($_POST['block_frei']) && isset($_POST['block_erweitern'])){
        $error = true;
        Form::error ("Bitte entweder um den nächsthöheren Block oder auf ABCDEF öffnen");
    }
    
    $tblock = $akt_turnier->details['tblock'];
    $fixed = $akt_turnier->details['tblock_fixed'];
    $art = $akt_turnier->details['art'];
    $erweitern = false; //Wird auf true gesetzt, wenn der Turnierblock erweitert werden soll
    
    //Um den nächst höheren Buchstaben erweitern
    if (isset($_POST['block_erweitern'])){
        if ($blockhoch){
            $chosen = array_search($akt_turnier->details['tblock'], Config::BLOCK_ALL);
            if (($_POST['block_erweitern'] ?? '') == Config::BLOCK_ALL[$chosen-1]){
                $tblock = Config::BLOCK_ALL[$chosen-1];
                $fixed = $akt_turnier->details['tblock_fixed'];
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
        if ($teamcenter){ //sollte übergeben werden
            $autor = $_SESSION['teamname'];
        }elseif ($ligacenter){
            $autor = "Ligaausschuss";
        }
        //Turnierblock erweitern
        if ($erweitern){
            $akt_turnier->change_turnier_block($tblock, $fixed, $art);
            $akt_turnier->details['tblock'] = $tblock;
            $akt_turnier->spieleliste_auffuellen(); //Spielen-Liste auffuellen
            $akt_turnier->schreibe_log("Turnierblock erweitert zu $tblock", $autor);
            if ($akt_turnier->details['tblock_fixed'] != $fixed){
                $akt_turnier->schreibe_log("Turnierfixierung geändert zu $fixed", $autor);
            }
            if ($akt_turnier->details['art'] != $art){
                $akt_turnier->schreibe_log("Turnierart geändert zu $art", $autor);
            }
            Form::affirm("Turnier wurde erweitert");
        }
        $mail = false;
        //Ändern der Turnierdetails
        if ($akt_turnier->change_turnier_details($startzeit, $besprechung, $plaetze, $spielplan, $hallenname, $strasse, $plz, $ort, $haltestellen, $hinweis, $startgebuehr, $organisator, $handy)){
            if ($akt_turnier->details['startzeit'] != $startzeit){
                $akt_turnier->schreibe_log("Startzeit: " . $akt_turnier->details['startzeit'] . " -> " . $startzeit, $autor);
                $mail = true;
            }
            if ($akt_turnier->details['plaetze'] != $plaetze){
                $akt_turnier->schreibe_log("Plätze: " . $akt_turnier->details['plaetze'] . " -> " . $plaetze, $autor);
                $mail = true;
            }
            if ($akt_turnier->details['besprechung'] != $besprechung){
                $akt_turnier->schreibe_log("Besprechung: " . $akt_turnier->details['besprechung'] . " -> " . $besprechung, $autor);
                //$mail = true;
            }          
            if ($akt_turnier->details['hinweis'] != stripcslashes($hinweis)){
                $akt_turnier->schreibe_log("Hinweis:\r\n" . $akt_turnier->details['hinweis'] . "\r\n->\r\n" . $hinweis, $autor);
                //$mail = true;
            }
            if ($akt_turnier->details['hallenname'] != $hallenname){
                $akt_turnier->schreibe_log("Hallenname: " . $akt_turnier->details['hallenname'] . " -> " . $hallenname, $autor);
                //$mail = true;
            }
            if ($akt_turnier->details['plz'] != $plz){
                $akt_turnier->schreibe_log("PLZ: " . $akt_turnier->details['plz'] . " -> " . $plz, $autor);
                $mail = true;
            }
            if ($akt_turnier->details['strasse'] != $strasse){
                $akt_turnier->schreibe_log("Straße: " . $akt_turnier->details['strasse'] . " -> " . $strasse, $autor);
                $mail = true;
            }
            if ($akt_turnier->details['ort'] != $ort){
                $akt_turnier->schreibe_log("Ort: " . $akt_turnier->details['ort'] . " -> " . $ort, $autor);
                $mail = true;
            }
            if ($akt_turnier->details['haltestellen'] != $haltestellen){
                $akt_turnier->schreibe_log("Haltestellen: " . $akt_turnier->details['haltestellen'] . " -> " . $haltestellen, $autor);
                //$mail = true;
            }
            if ($akt_turnier->details['organisator'] != $organisator){
                $akt_turnier->schreibe_log("Organisator: " . $akt_turnier->details['organisator'] . " -> " . $startzeit, $autor);
                //$mail = true;
            }
            if ($akt_turnier->details['handy'] != $handy){
                $akt_turnier->schreibe_log("Handy: " . $akt_turnier->details['handy'] . " -> " . $handy, $autor);
                //$mail = true;
            }
            if ($akt_turnier->details['startgebuehr'] != $startgebuehr){
                $akt_turnier->schreibe_log("Startgebühr: " . $akt_turnier->details['startgebuehr'] . " -> " . $startgebuehr, $autor);
                //$mail = true;
            }
            if ($akt_turnier->details['spielplan'] != $spielplan){
                $akt_turnier->schreibe_log("Spielplan: " . $akt_turnier->details['spielplan'] . " -> " . $spielplan, $autor);
                $mail = true;
            }
        }
        if ($mail or $erweitern){
            MailBot::mail_turnierdaten_geaendert($akt_turnier);
        }
        Form::affirm("Turnierdaten wurden geändert");
        header ('Location: ../liga/turnier_details.php?turnier_id=' . $akt_turnier->details['turnier_id']);
        die();
    }else{
        Form::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}