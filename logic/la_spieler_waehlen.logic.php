<?php

$spielers = nSpieler::get_all(); //Liste aller Spielernamen und IDs [0] => vorname nachname [1] => spieler_id

// ausgewählten Spieler finden
if (isset($_POST['spieler_auswahl'])) {
    $spieler_id = (explode(" | ", $_POST['spieler_auswahl']))[0]; // SpielerID extrahieren
    Helper::reload(get: "?spieler_id=" . $spieler_id);
}

// id in get-Var umwandeln
if (isset($_GET['spieler_id'])) {

    $spieler = nSpieler::get((int)$_GET['spieler_id']);
    if  (!isset($spieler->spieler_id)) {
        Html::error("Spieler wurde nicht gefunden");
    }

}