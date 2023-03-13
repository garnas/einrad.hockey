<?php

use App\Service\Team\Teamstats;

$team_id = $_SESSION['logins']['team']['id'];
$stats = new Teamstats($team_id);

$spiele = $stats->get_anzahl_spiele();
$alle = $stats->get_spiele_verteilung(2);
$schwach = $stats->get_spiele_verteilung(0);
$stark = $stats->get_spiele_verteilung(1);

$quote_stark = $stark['win'] / $alle['win'] * 100;
$quote_schwach = $schwach['win'] / $alle['win'] * 100;
$quote_gesamt = $alle['win'] / $spiele * 100;
