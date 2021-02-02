<?php
//Spielplan-Objekt aus Url-TurnierID erstellen
$turnier_id = $_GET['turnier_id'] ?? 0;


// Gibt es einen Spielplan zu diesem Turnier?

if (!Spielplan::check_exist($turnier_id)) {
    Form::error("Spielplan wurde nicht gefunden");
    header('Location: ../liga/turniere.php');
    die();
}
$spielplan = new Spielplan((new Turnier ($turnier_id)));

// Spielplanpause feststellen

$spiel_dauer = ($spielplan->details['anzahl_halbzeiten'] * $spielplan->details['halbzeit_laenge']
        + $spielplan->details['pause']) * 60; // Spieldauer in Sekunden;
$get_pause = function ($spiel) use ($spielplan, $spiel_dauer): int {
    return strtotime($spielplan->spiele[$spiel['spiel_id'] + 1]['zeit'] ?? $spiel['zeit']) //Uhrzeit des nächsten Spiels
        - strtotime($spiel['zeit'])  // Zeit dieses Spiels
        - $spiel_dauer;
};


// Pixellänge des längsten Teamnamens für die perfekte Darstellung des Spielplans (Tore, Schiris, Penaltys in Desktop)
$function = function ($platz) {
    return strlen($platz['teamname']);
};
$teamnamen_laengen = array_map(function ($platz) {
    return strlen($platz['teamname']);
}, $spielplan->platzierungstabelle);
$width_in_px = max($teamnamen_laengen) * 8; // 7.5 Durchschnittliche px-Weite eines Characters