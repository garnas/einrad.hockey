<?php
$abstimmung = new Abstimmung();

if(isset($_SESSION['team_id'])) {
    $team_id = $_SESSION["team_id"];
    $stimme_check = $abstimmung->get_team($team_id);
}
