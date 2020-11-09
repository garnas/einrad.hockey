<?php
//Formularauswertung
if (isset($_POST['create_team'])) {
    $error = false;
    //Validierung alle Eingaben gemacht
    if ($_POST['gruppenname']==false) {
        $error = true;
        Form::error ("Gruppenname fehlt");
    }
    if ($_POST['nameBot']==false) {
        $error = true;
        Form::error ("Name vom Email-Bot fehlt");
    }
    if ($_POST['emailBot']==false) {
        $error = true;
        Form::error ("Emailadresse vom Bot fehlt");
    }
    if ($_POST['alias']==false) {
        $error = true;
        Form::error ("Alias fehlt");
    }
    if ($_POST['vorname']==false) {
        $error = true;
        Form::error ("Vorname fehlt");
    }
    if ($_POST['nachname']==false) {
        $error = true;
        Form::error ("Nachname fehlt");
    }
    if ($_POST['email']==false) {
        $error = true;
        Form::error ("Emailadresse fehlt");
    }

    //Eintragen des Turnieres
    if (!$error){
        // //Turnier erstellen
        // $turnier_id = db::get_auto_increment("turniere_liga");
        // if (Turnier::create_turnier($tname, $ausrichter_team_id, $startzeit, $besprechung, $art, $tblock, $fixed, $datum ,$plaetze, $spielplan, $hallenname, $strasse, $plz, $ort, $haltestellen, $hinweis, $startgebuehr, $organisator, $handy, $phase)){
        //     Form::affirm ("Euer Turnier wurde erfolgreich eingetragen!");
        //     $akt_turnier = new Turnier($turnier_id);
        //     if ($ligacenter == true){
        //         $autor = "Ligaausschuss";
        //     }elseif ($teamcenter == true){
        //         $autor = $_SESSION['teamname'];
        //     }else{
        //         $autor = "Fehler! Team- oder Ligacenter konnte nicht ermittelt werden!";
        //     }
        //     //Logs schreiben
        //     $akt_turnier->schreibe_log( "Turnier wurde erstellt",  $autor);
        //     $akt_turnier->schreibe_log( "Turniername: $tname\r\nAusrichter: $ausrichter_name\r\nStartzeit: $startzeit\r\nBesprechung: $besprechung\r\nArt: $art\r\nBlock: $tblock\r\n" .
        //                                 "Fixiert: $fixed\r\nDatum: $datum \r\nPlätze: $plaetze\r\nSpielplan: $spielplan\r\nHallenname: $hallenname\r\n".
        //                                 "Straße: $strasse\r\nPlz: $plz\r\nOrt: $ort\r\nHaltestellen: $haltestellen\r\nHinweis: $hinweis\r\nStartgebühr: $startgebuehr\r\n".
        //                                 "Organisator: $organisator\r\nHandy: $handy", $autor);
        //     $akt_turnier->schreibe_log( "Anmeldung als Ausrichter:\r\n" . $ausrichter_name . " -> Liste: spiele", $autor);
        //     //Mailbot
        //     if ($teamcenter){ //Es wird nur eine Mail verschickt, wenn ein Turnier im Teamcenter erstellt wurde
        //         MailBot::mail_neues_turnier($akt_turnier);
        //     }
        //     header('Location: ../liga/turnier_details.php?turnier_id=' . $turnier_id);
        //     die();
        // }
    }else{
        Form::error("Es ist ein Fehler aufgetreten. Gruppe wurde nicht erstellt.");
    }
}
