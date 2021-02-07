<?php
// Kann das Turnier erweitert werden?
if ($turnier->details['phase'] == 'melde' && strlen($turnier->details['tblock']) < 3 && $turnier->details['tblock'] != 'AB' && $turnier->details['tblock'] != 'A' && ($turnier->details['art'] == 'I' or $turnier->details['art'] == 'II')){
    $blockhoch = true;
}else{
    $blockhoch = false;
}
// Kann das Turnier auf ABCDEF erweitert werden?
if ($turnier->details['phase'] == 'melde' && $turnier->details['art'] != 'III' && ($turnier->details['art'] == 'I' or $turnier->details['art'] == 'II')){
    $blockfrei = true;
}else{
    $blockfrei = false;
}

// Formularauswertung
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
    
    // Besprechung
    if (($_POST['besprechung'] ?? '') == 'Ja'){
        $besprechung = 'Ja';
    }else{
        $besprechung = 'Nein';
    }

    // Anzahl der Plätze bzw ob 8er DKO- oder Gruppen-Spielplan
    if ($plaetze == '8 dko'){
        $plaetze = 8;
        $spielplan = 'dko';
    }elseif ($plaetze == '8 gruppen'){
        $plaetze = 8;
        $spielplan = 'gruppen';
    }else{
        $spielplan = 'jgj';
    }

    // Leere Felder können eigentlich nicht auftreten (nur durch html-Manipulation), aber sicherheitshalber dass hier...
    if (empty($plaetze) or empty($startzeit) or empty($hallenname) or empty($strasse) or empty($plz) or empty($ort) or empty($hinweis) or empty($organisator) or empty($handy)){
        $error = true;
        Form::error("Bitte alle nicht optionalen Felder ausfüllen.");
    }

    // Validierung Startzeit:
    if ($startzeit != $turnier->details['startzeit']  && $teamcenter){
        if ($turnier->details['art'] == 'final'){
            $error = true;
            Form::error("Die Startzeit bei Abschlussturnieren kann nur vom Ligaausschuss geändert werden.");
        }
        if ($startzeit != $turnier->details['startzeit'] && (date("H", strtotime($startzeit)) < 9 or date("H", strtotime($startzeit)) > 14) && $teamcenter){
            $error = true;
            Form::error("Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen spätestens um 20:00&nbsp;Uhr beendet sein");
        }
    }

    // Validierung der Plätze
    if ($plaetze != $turnier->details['plaetze']  && $teamcenter){
        if ($turnier->details['art'] == 'final'){ //Anzahl der Plätze darf nur geändert werden, wenn es sich nicht um ein Finalturnier handelt
            Form::error("Das Ändern der Anzahl der Plätze ist bei Abschlussturnieren können nur vom Ligaausschuss geändert werden.");
            $error = true;
        }
        if ($plaetze < 5 or $plaetze > 8){
            $error = true; // 4er nur via Ligaausschuss
            Form::error("Ungültige Anzahl an Turnierplätzen");
        }
    }

    // Keine Änderung der Plätze in der Spielplanphase
    if ($turnier->details['phase'] == 'spielplan'){
        if ($turnier->details['plaetze'] != $plaetze && $teamcenter){
            $error = true;
            Form::error("Die Anzahl der Plätze kann in der Spielplanphase nicht mehr geändert werden. Bitte wende dich unter ".Config::LAMAIL." an den Ligaaussschuss.");
        }
    }
    // Keine Änderung der Plätze in der Spielplanphase
    if ($turnier->details['phase'] == 'ergebnis' && $teamcenter){
        $error = true;
        Form::error("Turniere können in der Ergebnisphase nicht mehr geändert werden. Bitte wende dich unter ".Config::LAMAIL." an den Ligaaussschuss.");
    }

    // Block erweitern

    // Es wurden beide Häkchen gesetzt
    if (isset($_POST['block_frei']) && isset($_POST['block_erweitern'])){
        $error = true;
        Form::error ("Bitte entweder um den nächsthöheren Block oder auf ABCDEF öffnen");
    }
    
    $tblock = $turnier->details['tblock'];
    $fixed = $turnier->details['tblock_fixed'];
    $art = $turnier->details['art'];
    $erweitern = false; // Wird auf true gesetzt, wenn der Turnierblock erweitert werden soll
    
    // Um den nächst höheren Buchstaben erweitern
    if (isset($_POST['block_erweitern'])){
        if ($blockhoch){
            $chosen = array_search($turnier->details['tblock'], Config::BLOCK_ALL);
            if (($_POST['block_erweitern'] ?? '') == Config::BLOCK_ALL[$chosen-1]){
                $tblock = Config::BLOCK_ALL[$chosen-1];
                $fixed = $turnier->details['tblock_fixed'];
                $erweitern = true;
            }
        }else{
            $error = true;
            Form::error ("Das Turnier kann nicht um den nächsthöheren Block erweitert werden.");
        }
    }

    // Auf ABCDEF erweitern
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

    // Ändern der Turnierdaten
    if (!$error){
        // Autor der Turnierlogs festlegen
        if ($teamcenter){ // sollte übergeben werden
            $autor = $_SESSION['teamname'];
        }elseif ($ligacenter){
            $autor = "Ligaausschuss";
        }
        // Turnierblock erweitern
        if ($erweitern){
            $turnier->change_turnier_block($tblock, $fixed, $art);
            $turnier->details['tblock'] = $tblock;
            $turnier->spieleliste_auffuellen(); // Spielen-Liste auffuellen
            $turnier->log("Turnierblock erweitert zu $tblock", $autor);
            if ($turnier->details['tblock_fixed'] != $fixed){
                $turnier->log("Turnierfixierung geändert zu $fixed", $autor);
            }
            if ($turnier->details['art'] != $art){
                $turnier->log("Turnierart geändert zu $art", $autor);
            }
            Form::affirm("Turnier wurde erweitert");
        }
        $mail = false;
        // Ändern der Turnierdetails
        if ($turnier->change_turnier_details($startzeit, $besprechung, $plaetze, $spielplan, $hallenname, $strasse, $plz, $ort, $haltestellen, $hinweis, $startgebuehr, $organisator, $handy)){
            if ($turnier->details['startzeit'] != $startzeit){
                $turnier->log("Startzeit: " . $turnier->details['startzeit'] . " -> " . $startzeit, $autor);
                $mail = true;
            }
            if ($turnier->details['plaetze'] != $plaetze){
                $turnier->log("Plätze: " . $turnier->details['plaetze'] . " -> " . $plaetze, $autor);
                $mail = true;
            }
            if ($turnier->details['besprechung'] != $besprechung){
                $turnier->log("Besprechung: " . $turnier->details['besprechung'] . " -> " . $besprechung, $autor);
                // $mail = true;
            }          
            if ($turnier->details['hinweis'] != stripcslashes($hinweis)){
                $turnier->log("Hinweis:\r\n" . $turnier->details['hinweis'] . "\r\n->\r\n" . $hinweis, $autor);
                // $mail = true;
            }
            if ($turnier->details['hallenname'] != $hallenname){
                $turnier->log("Hallenname: " . $turnier->details['hallenname'] . " -> " . $hallenname, $autor);
                // $mail = true;
            }
            if ($turnier->details['plz'] != $plz){
                $turnier->log("PLZ: " . $turnier->details['plz'] . " -> " . $plz, $autor);
                $mail = true;
            }
            if ($turnier->details['strasse'] != $strasse){
                $turnier->log("Straße: " . $turnier->details['strasse'] . " -> " . $strasse, $autor);
                $mail = true;
            }
            if ($turnier->details['ort'] != $ort){
                $turnier->log("Ort: " . $turnier->details['ort'] . " -> " . $ort, $autor);
                $mail = true;
            }
            if ($turnier->details['haltestellen'] != $haltestellen){
                $turnier->log("Haltestellen: " . $turnier->details['haltestellen'] . " -> " . $haltestellen, $autor);
                // $mail = true;
            }
            if ($turnier->details['organisator'] != $organisator){
                $turnier->log("Organisator: " . $turnier->details['organisator'] . " -> " . $startzeit, $autor);
                // $mail = true;
            }
            if ($turnier->details['handy'] != $handy){
                $turnier->log("Handy: " . $turnier->details['handy'] . " -> " . $handy, $autor);
                // $mail = true;
            }
            if ($turnier->details['startgebuehr'] != $startgebuehr){
                $turnier->log("Startgebühr: " . $turnier->details['startgebuehr'] . " -> " . $startgebuehr, $autor);
                // $mail = true;
            }
            if ($turnier->details['spielplan'] != $spielplan){
                $turnier->log("Spielplan: " . $turnier->details['spielplan'] . " -> " . $spielplan, $autor);
                $mail = true;
            }
        }
        if ($mail or $erweitern){
            MailBot::mail_turnierdaten_geaendert($turnier);
        }
        Form::affirm("Turnierdaten wurden geändert");
        header ('Location: ../liga/turnier_details.php?turnier_id=' . $turnier->details['turnier_id']);
        die();
    }else{
        Form::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}