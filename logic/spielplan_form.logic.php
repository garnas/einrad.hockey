<?php

if (isset($_POST["tore_speichern_oben"]) or isset($_POST["tore_speichern_unten"])) {
    // Neu eingetragene Tore speichern
    foreach ($spiele as $spiel_id => $spiel) {
        if ($spiel['tore_a'] == $_POST["tore_a"][$spiel_id]
            && $spiel['tore_b'] == $_POST["tore_b"][$spiel_id]
            && ($spiel['penalty_a'] ?? '') == ($_POST["penalty_a"][$spiel_id] ?? '')
            && ($spiel['penalty_b'] ?? '') == ($_POST["penalty_b"][$spiel_id] ?? '')) continue;
        $spielplan->set_spiele(
            $spiel['spiel_id'],
            $_POST["tore_a"][$spiel_id],
            $_POST["tore_b"][$spiel_id],
            $_POST["penalty_a"][$spiel_id] ?? '',
            $_POST["penalty_b"][$spiel_id] ?? '',
        );
    }
    Form::affirm('Spielergebnisse wurden gespeichert');
    header('Location: ' . db::escape($_SERVER['REQUEST_URI']));
    die();
}

//Turnierergebnisse speichern
if (isset($_POST["turnierergebnis_speichern"])) {

    // Sind alle Spiele gespielt und kein Penalty offen
    if (!$spielplan->check_turnier_beendet()) {
        Form::error("Es sind noch Spiel- oder Penaltyergebnisse offen. Turnierergebnisse wurden nicht übermittelt.");
        $error = true;
    }

    // Testen ob Turnier eingetragen werden darf
    if (!Tabelle::check_ergebnis_eintragbar($spielplan->turnier)) {
        Form::error("Turnierergebnis konnte nicht eingetragen werden. Kontaktiere bitte den Ligaausschuss.");
        $error = true;
    }

    // Sind alle spiele gespielt und kein Penalty mehr notwendig
    if (!$error ?? false) {
        $spielplan->set_ergebnis();
        header('Location: ' . db::escape($_SERVER['REQUEST_URI']));
        die();
    }

}

// Hinweis Penalty
#$spielplan->filter_penalty_begegnungen(); // Nicht endgültige Penaltys werden gelöscht.
foreach ($spielplan->ausstehende_penalty_begegnungen as $key => $penalty){
    Form::attention("Penalty-Schießen zwischen " . implode(" und ", $penalty));
}

// Hinweis Kaderkontrolle und Turnierreport
if (!(new TurnierReport($turnier_id))->kader_check()) {
    Form::affirm("Bitte kontrolliert die Teamkader und setzt im " . Form::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, 'Turnierreport') . " das entsprechende Häkchen.");
}

if(!$spielplan->check_penalty_ergebnisse()){
    Form::error("Achtung: Es liegen falsch eingetragene Penaltyergebnisse vor!.");
}