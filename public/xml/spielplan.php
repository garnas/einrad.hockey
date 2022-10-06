<?php
use Spatie\ArrayToXml\ArrayToXml;

require_once '../../init.php';
Helper::$log_user = false; // Keine User-Logs


$turnier_id = (int) @$_GET['turnier_id'];

// Gibt es einen Spielplan zu diesem Turnier?
if (!Spielplan::check_exist($turnier_id)) {
    Helper::not_found("Spielplan wurde nicht gefunden");
}

// Spielbegegnungen laden
$turnier = nTurnier::get($turnier_id);
$spielplan = new Spielplan_JgJ($turnier);
$spiele = $spielplan->get_spiele();

// Wichtige Infos zu den Spielen hinzufügen
$details = $spielplan->get_details();
foreach ($spiele as $key => $spiel) {
    $spiele[$key]["anzahl_halbzeiten"] = $details["anzahl_halbzeiten"];
    $spiele[$key]["halbzeit_laenge"] = $details["halbzeit_laenge"]; # Länger der Halbzeit in Minuten
    $spiele[$key]["puffer"] = $details["puffer"]; # Puffer für jedes Spiel in Minuten
    $spiele[$key]["farbe_a"] = $spielplan->get_trikot_colors($spiel, false)[$spiel['team_id_a']] ?? '';
    $spiele[$key]["farbe_b"] = $spielplan->get_trikot_colors($spiel, false)[$spiel['team_id_b']] ?? '';
    $spiele[$key]["schiri_teamname_a"] = Team::id_to_name($spiel['schiri_team_id_a']);
    $spiele[$key]["schiri_teamname_b"] = Team::id_to_name($spiel['schiri_team_id_b']);
}

// Values in String casten, für Xml-Erstellung
array_walk_recursive($spiele, static function (&$value) { $value = (string)$value; });

// Array als XML ausgeben
$spiele = ArrayToXml::convert(
    ['spiel' => $spiele],
    'spielplan',
    false,
    'UTF-8',
    '1.0',
    [],
    null
);

header('Content-type: text/xml');
echo $spiele;