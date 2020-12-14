<?php
require_once '../../logic/first.logic.php'; // Autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php'; // Logic der Challenge

$spieler_id = 874;
$urkunden_daten = $challenge->get_spieler_result($spieler_id);

if ($urkunden_daten['geschlecht'] == "m") {
    $anrede = "Der Spieler";
} else {
    $anrede = "Die Spielerin";
}

?>

<p><?=$anrede . ' ' . $urkunden_daten['vorname'] . ' ' . $urkunden_daten['nachname'];?> (<?=$urkunden_daten['teamname']?>) hat mit <?=$urkunden_daten['kilometer']?> den <?=$urkunden_daten['platz']?>. Platz erreicht!</p>