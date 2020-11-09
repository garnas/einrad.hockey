<?php
//Formularauswertung
if (isset($_POST['create_team']) && !$akt_team->get_terminplaner()) {
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
        Form::affirm("Du hast eine Email mit weiteren Instruktionen erhalten");
        $akt_team->set_terminplaner();
    }else{
        Form::error("Es ist ein Fehler aufgetreten. Gruppe wurde nicht erstellt.");
    }
}
