<?php # -*- php -*-

require_once '../../init.php'; # Autoloader und Session, muss immer geladen werden!

##############################################################################

# Hier Prüfling und Schwierigkeitsgrad definieren, dann php-Skript starten:

# Spieler-ID (oder Name) des Prüflings:
$pruefling = 'Erika Mustermann';

# Schwierigkeitsgrad des Tests auswählen:
#$level = 'J'; # Junior
#$level = 'B'; # Basis
$level = 'F'; # Fortgeschrittene

##############################################################################

$levelname = array('J'=>'Junior', 'B'=>'Basis', 'F'=>'Fortgeschrittene');

$fragen01 = SchiriTest::get_fragen($level,  '1', 2); # Vor dem Spiel / Rund ums Spiel
$fragen02 = SchiriTest::get_fragen($level,  '2', 3); # Schiedsrichterverhalten
$fragen03 = SchiriTest::get_fragen($level,  '3', 1); # Handzeichen
$fragen04 = SchiriTest::get_fragen($level,  '4', 1); # Penaltyschießen
$fragen05 = SchiriTest::get_fragen($level,  '5', 3); # Vorfahrt
$fragen06 = SchiriTest::get_fragen($level,  '6', 3); # Übertriebene Härte
$fragen07 = SchiriTest::get_fragen($level,  '7', 3); # Eingriff ins Spiel
$fragen08 = SchiriTest::get_fragen($level,  '8', 6); # Sonstige Fouls
$fragen09 = SchiriTest::get_fragen($level,  '9', 4); # Torschüsse
$fragen10 = SchiriTest::get_fragen($level, '10', 1); # Zeitstrafen/Unsportlichkeiten
$fragen11 = SchiriTest::get_fragen($level, '11', 3); # Strafen
$fragen = $fragen01 + $fragen02 + $fragen03 + $fragen04 + $fragen05 + $fragen06 +
    $fragen07 + $fragen08 + $fragen09 + $fragen10 + $fragen11;

$fragen_csv = implode(',',array_keys($fragen));

$datum = date('Y-m-d');

echo 'Es wurde folgender Test erstellt:<p>';

echo 'Prüfling: ' . $pruefling . '<p>';

echo 'Schwierigkeitsgrad: ' . $levelname[$level] . '<p>';

echo 'Datum: ' . $datum . '<p>';

echo 'Fragen-IDs: ' . $fragen_csv . '<p>';

echo 'Die folgende URL kann an den Prüfling verschickt werden:<p>';

# lokaler Webserver:
echo 'http://localhost/einrad.hockey/public/schiricenter/basistest-personifiziert.php?md5sum='
    . md5($fragen_csv) . '<p>';

# Webserver der Liga:
echo 'https://test.einrad.hockey/schiricenter/basistest-personifiziert.php?md5sum='
    . md5($fragen_csv) . '<p>';
