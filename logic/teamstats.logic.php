<?php

use App\Service\Team\Teamstats;
use App\Repository\Team\TeamRepository;

$team_id = $_SESSION['logins']['team']['id'];
$stats = new Teamstats($team_id);
$eval = true;

$spiele = $stats->get_anzahl_spiele();
if ($spiele == 0) $eval = false;

// Spiele gegen alle Teams
$alle = $stats->get_verteilung_spiele(Teamstats::ALLE);
// Spiele, bei denen man das schwaechere Team gewesen ist
$schwach = $stats->get_verteilung_spiele(Teamstats::SCHWACH);
// Spiele, bei denen man das staerkere Team gewesen ist
$stark = $stats->get_verteilung_spiele(Teamstats::STARK);

$alle_tore = $stats->get_verteilung_tore(Teamstats::ALLE);
$schwach_tore = $stats->get_verteilung_tore(Teamstats::SCHWACH);
$stark_tore = $stats->get_verteilung_tore(Teamstats::STARK);

$hoechster_sieg = $stats->get_hoechster_sieg();
$hoechster_sieg_string = is_null($hoechster_sieg) ? "-" :  $hoechster_sieg['tore_a'] . ":" . $hoechster_sieg['tore_b'];
$hoechster_sieg_substring = TeamRepository::get()->team($hoechster_sieg['team_id_b'])->getName();

$hoechste_niederlage = $stats->get_hoechste_niederlage();
$hoechste_niederlage_string = is_null($hoechste_niederlage) ? "-" :  $hoechste_niederlage['tore_a'] . ":" . $hoechste_niederlage['tore_b'];
$hoechste_niederlage_substring = TeamRepository::get()->team($hoechste_niederlage['team_id_b'])->getName();

$bestes_turnier = $stats->get_bestes_turnier();
$bestes_turnier_string = is_null($bestes_turnier) ? "-" : $bestes_turnier['ergebnis'];
$bestes_turnier_subtext = date_format(date_create($bestes_turnier['datum']), 'd.m.Y') . " in " . $bestes_turnier['ort'];

$schlechtestes_turnier = $stats->get_schlechtestes_turnier();
$schlechtestes_turnier_string = is_null($schlechtestes_turnier) ? "-" : $schlechtestes_turnier['ergebnis'];
$schlechtestes_turnier_subtext = date_format(date_create($schlechtestes_turnier['datum']), 'd.m.Y') . " in " . $schlechtestes_turnier['ort'];


$teams = $stats->get_gegner();
$angst = $stats->get_angstgegner();
if (!is_null($angst)) {
    $first_angst = array_shift($angst);
}
$liebling = $stats->get_lieblingsgegner();
if (!is_null($liebling)) {
    $first_liebling = array_shift($liebling);
}