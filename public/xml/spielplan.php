<?php

use App\Repository\Turnier\TurnierRepository;
use App\Service\Spielplan\SpielplanFactory;
use App\Service\Spielplan\SpielplanService;
use Spatie\ArrayToXml\ArrayToXml;

require_once '../../init.php';
Helper::$log_user = false; // Keine User-Logs


$turnier_id = (int) @$_GET['turnier_id'];

// Gibt es einen Spielplan zu diesem Turnier?
if (!SpielplanService::hatSpielplan($turnier_id)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}

$turnier = TurnierRepository::get()->turnier($turnier_id);
$spielplan = SpielplanFactory::ausTurnier($turnier);
$details = $spielplan->getDetails();

$spiele = [];
foreach ($spielplan->getSpiele() as $spiel_id => $spiel) {
    $farben = $spielplan->getTrikotColors($spiel, false);
    $spiele[$spiel_id] = [
        'spiel_id' => $spiel->getSpielId(),
        'zeit' => $spiel->getZeit(),
        'team_id_a' => $spiel->getTeamIdA(),
        'teamname_a' => $spiel->getTeamnameA(),
        'team_id_b' => $spiel->getTeamIdB(),
        'teamname_b' => $spiel->getTeamnameB(),
        'schiri_team_id_a' => $spiel->getSchiriIdA(),
        'schiri_team_id_b' => $spiel->getSchiriIdB(),
        'schiri_teamname_a' => $spiel->getSchiriNameA(),
        'schiri_teamname_b' => $spiel->getSchiriNameB(),
        'tore_a' => $spiel->getToreA(),
        'tore_b' => $spiel->getToreB(),
        'penalty_a' => $spiel->getPenaltyA(),
        'penalty_b' => $spiel->getPenaltyB(),
        'anzahl_halbzeiten' => $details->getAnzahlHalbzeiten(),
        'halbzeit_laenge' => $details->getHalbzeitLaenge(), # Länge der Halbzeit in Minuten
        'puffer' => $details->getPuffer(), # Puffer für jedes Spiel in Minuten
        'farbe_a' => $farben[$spiel->getTeamIdA()] ?? '',
        'farbe_b' => $farben[$spiel->getTeamIdB()] ?? '',
    ];
}

// Values in String casten, für Xml-Erstellung
array_walk_recursive($spiele, static function (&$value) {
    $value = (string) $value;
});

// Array als XML ausgeben
$spiele = ArrayToXml::convert(
    ['spiel' => $spiele],
    'spielplan',
    false,
    'UTF-8',
    '1.0',
    [],
    null,
);

header('Content-type: text/xml');
echo $spiele;
