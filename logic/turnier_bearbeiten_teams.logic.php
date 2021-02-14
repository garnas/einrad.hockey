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
    if ($startzeit != $turnier->details['startzeit']  && Config::$teamcenter){
        if ($turnier->details['art'] == 'final'){
            $error = true;
            Form::error("Die Startzeit bei Abschlussturnieren kann nur vom Ligaausschuss geändert werden.");
        }
        if ($startzeit != $turnier->details['startzeit'] && (date("H", strtotime($startzeit)) < 9 or date("H", strtotime($startzeit)) > 14) && Config::$teamcenter){
            $error = true;
            Form::error("Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen spätestens um 20:00&nbsp;Uhr beendet sein");
        }
    }

    // Validierung der Plätze
    if ($plaetze != $turnier->details['plaetze']  && Config::$teamcenter){
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
        if ($turnier->details['plaetze'] != $plaetze && Config::$teamcenter){
            $error = true;
            Form::error("Die Anzahl der Plätze kann in der Spielplanphase nicht mehr geändert werden. Bitte wende dich unter ".Config::LAMAIL." an den Ligaaussschuss.");
        }
    }
    // Keine Änderung der Plätze in der Spielplanphase
    if ($turnier->details['phase'] == 'ergebnis' && Config::$teamcenter){
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
        // Turnierblock erweitern
        if ($erweitern){
            $turnier->change_turnier_block($tblock, $fixed, $art);
            $turnier->spieleliste_auffuellen(); // Spielen-Liste auffuellen
            Form::info("Turnier wurde erweitert");
        }

        // Ändern der Turnierdetails
        $wichtiges_geaendert = $turnier->change_turnier_details(
                $startzeit, $besprechung, $plaetze, $spielplan, $hallenname, $strasse,
                $plz, $ort, $haltestellen, $hinweis, $startgebuehr, $organisator, $handy);

        // Mail an den Ligaausschuss?
        if (($wichtiges_geaendert or $erweitern)
            && Config::$teamcenter) MailBot::mail_turnierdaten_geaendert($turnier);

        Form::info("Turnierdaten wurden geändert");
        header ('Location: ../liga/turnier_details.php?turnier_id=' . $turnier->id);
        die();
    }else{
        Form::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}