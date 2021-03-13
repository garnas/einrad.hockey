<?php
// Formularauswertung
if (isset($_POST['create_team']) && !$akt_team->check_terminplaner()) {
    $error = false;
    // Validierung alle Eingaben gemacht
    if ($_POST['gruppenname'] == false) {
        $error = true;
        Html::error("Gruppenname fehlt");
    }
    if ($_POST['alias'] == false) {
        $error = true;
        Html::error("Alias fehlt");
    }
    if ($_POST['vorname'] == false) {
        $error = true;
        Html::error("Vorname fehlt");
    }
    if ($_POST['nachname'] == false) {
        $error = true;
        Html::error("Nachname fehlt");
    }
    if ($_POST['email'] == false) {
        $error = true;
        Html::error("E-Mail-Adresse fehlt");
    }

    // Eintragen des Turnieres
    if ($error) {
        Html::error("Es ist ein Fehler aufgetreten. Gruppe wurde nicht erstellt.");
    } else {
        Html::info("Du hast eine Email mit weiteren Instruktionen erhalten.");
        $team->set_terminplaner();
        Helper::reload();
    }
}
