<?php

use App\Service\Team\Teamstats;

$team_id = $_SESSION['logins']['team']['id'];
$stats = new Teamstats($team_id);
$eval = true;

$spiele = $stats->get_anzahl_spiele();
if ($spiele == 0) $eval = false;

if ($eval) {
    $alle = $stats->get_verteilung_spiele(Teamstats::ALLE);
    $schwach = $stats->get_verteilung_spiele(Teamstats::SCHWACH);
    $stark = $stats->get_verteilung_spiele(Teamstats::STARK);

    $quote_stark = number_format($stark['win'] / $alle['win'] * 100, 2, ',', '.');
    $quote_schwach = number_format($schwach['win'] / $alle['win'] * 100, 2, ',', '.');
    $quote_gesamt = number_format($alle['win'] / $spiele * 100, 2, ',', '.');

}
