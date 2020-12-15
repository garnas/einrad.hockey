<?php
require_once '../../logic/first.logic.php'; // Autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php'; // Logic der Challenge

$team_id = $_GET['team_id'];
$urkunden_daten = $challenge->get_team_result($team_id);

?>

<p>Das Team <?=$urkunden_daten['teamname']?> hat mit <?=$urkunden_daten['kilometer']?> den <?=$urkunden_daten['platz']?>. Platz erreicht!</p>