<?php

function get_sum($arr) {
    $sum = 0;
    foreach ($arr as $key => $value) {
        $sum += $value;
    }

    return $sum == 0 ? 1 : $sum;
}

function get_average($arr) {
    $sum = 0;
    $count = 0;

    foreach ($arr as $key => $value) {
        if ($key > 0) {
            $sum += $value * $key;
            $count += $value;
        }
    }

    return $count == 0 ? 1 : round($sum / $count, 2);
}

use App\Repository\Team\TeamRepository;

$teams = TeamRepository::get()->activeLigaTeams();

$abschluss = strtotime(Abstimmung::ENDE);
$beginn = strtotime(Abstimmung::BEGINN);

$num_stimmen = Abstimmung::get_anzahl_stimmen();
$ergebnisse = Abstimmung::get_ergebnisse(5);

$massnahmen = array_slice($ergebnisse, 0, 6);
$weiteres = array_slice($ergebnisse, 6, 1);
$option = array_slice($ergebnisse, 7, 1);

$massnahme_text = [
    0 => 'Enthaltung',
    1 => '1 - weniger wichtig',
    5 => '5 - sehr wichtig'
];

$massnahme_key = [
    'Uebernachtungskosten' => 'Übernachtungskosten',
    'Aufwandsentschaedigung' => 'Aufwandsentschädigung'
];
