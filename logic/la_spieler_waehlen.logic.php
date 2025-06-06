<?php

use App\Repository\Spieler\SpielerRepository;

$spielerAlle = SpielerRepository::get()->getSpielerAndTeam();

# Ausgewälten Spieler laden
if (isset($_POST['spieler_auswahl'])) {
    $spieler_id = (explode(" | ", $_POST['spieler_auswahl']))[0]; // SpielerID extrahieren
    Helper::reload(get: "?spieler_id=" . $spieler_id);
}

if (isset($_GET['spieler_id'])) {
    $spieler = SpielerRepository::get()->spieler((int)$_GET['spieler_id']);
    if  ($spieler === null) {
        Html::error("Spieler wurde nicht gefunden");
    }
}
