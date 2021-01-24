<?php

// Besteht die Berechtigung das Turnier zu bearbeiten?
if(!$ligacenter){ // Ligacenter darf alles.
    if (($teamcenter && ($_SESSION['team_id'] ?? false) != $spielplan->turnier->details['ausrichter'])){
        Form::error("Nur der Ausrichter kann Spielergebnisse eintragen");
        header('Location: ../liga/spielplan.php?turnier_id=' . $turnier_id);
        die();
    }

    // Wird das Turnierergebnis rechtzeitig eingetragen?
    $date = $spielplan->turnier->details['datum'];
    $N = date("N", strtotime($date)); // Numerischer Wochentag.
    $delta = (8-$N) * 24*60*60 + 18*60*60; // Die Zeit bis zum nächsten Montag 18:00 Uhr von 0:00 Uhr aus gesehen.
    $abgabe = strtotime($date) + $delta;
    if ($abgabe > Config::time_offset()){
        Form::error("Bitte wende dich an den Ligaausschuss um Ergebnisse nachträglich zu verändern.");
        header('Location: ../liga/spielplan.php?turnier_id=' . $turnier_id);
        die();
    }
}

if (isset($_POST["tore_speichern"])) {
    // Neu eingetragene Tore speichern
    foreach ($spielplan->spiele as $spiel_id => $spiel) {
        if ($spiel['tore_a'] == $_POST["tore_a"][$spiel_id]
            && $spiel['tore_b'] == $_POST["tore_b"][$spiel_id]
            && $spiel['penalty_a'] == ($_POST["penalty_a"][$spiel_id]  ?? '')
            && $spiel['penalty_b'] == ($_POST["penalty_b"][$spiel_id]  ?? '')) continue;
        $spielplan->set_spiele(
            $spiel['spiel_id'],
            $_POST["tore_a"][$spiel_id],
            $_POST["tore_b"][$spiel_id],
            $_POST["penalty_a"][$spiel_id] ?? 'NULL',
            $_POST["penalty_b"][$spiel_id] ?? 'NULL'
        );
    }
    Form::affirm('Spielergebnisse wurden gespeichert');
    header('Location: ' . db::escape($_SERVER['REQUEST_URI']));
    die();
}

//Turnierergebnisse speichern
if (isset($_POST["turnierergebnis_speichern"])) {

    // Sind alle Spiele gespielt und kein Penalty offen?
    if (!$spielplan->check_turnier_beendet()) {
        Form::error("Es sind noch Spiel- oder Penaltyergebnisse offen. Turnierergebnisse wurden nicht übermittelt.");
        $error = true;
    }

    // Testen ob Turnier tabellentechnisch eingetragen werden darf.
    if (!Tabelle::check_ergebnis_eintragbar($spielplan->turnier)) {
        Form::error("Turnierergebnis konnte nicht eingetragen werden. Kontaktiere bitte den Ligaausschuss.");
        $error = true;
    }

    if (!$error ?? false) {
        $spielplan->set_ergebnis();
        header('Location: ' . db::escape($_SERVER['REQUEST_URI']));
        die();
    }

}

// Hinweis Kaderkontrolle und Turnierreport
if (!(new TurnierReport($turnier_id))->kader_check()) {
    Form::affirm("Bitte kontrolliert die Teamkader und setzt im " . Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, 'Turnierreport') . " das entsprechende Häkchen.");
}

if(!$spielplan->check_penalty_ergebnisse()){
    Form::error("Achtung: Es liegen falsch eingetragene Penaltyergebnisse vor!.");
}