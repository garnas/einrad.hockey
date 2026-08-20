<?php

/**
 * Legt (bzw. erneuert) das Fixture-Team samt Kader für tc_turnier_erstellen.php an.
 * Wird vor dem Testlauf per CLI ausgeführt (siehe global-setup.ts).
 *
 * Braucht mindestens fünf Kaderspieler der aktuellen Saison, da tc_turnier_erstellen.php
 * sonst zu tc_kader.php umleitet (siehe Zeile 12 der geprüften Datei).
 */

use App\Entity\Team\nTeam;
use App\Entity\Team\Spieler;
use App\Entity\Team\TeamDetails;
use App\Entity\Turnier\Turnier;
use App\Repository\DoctrineWrapper;
use App\Repository\Spieler\SpielerRepository;
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;

require_once __DIR__ . '/../../../init.php';

$teamName = $argv[1] ?? throw new InvalidArgumentException('Teamname fehlt.');
$teamPasswort = $argv[2] ?? throw new InvalidArgumentException('Passwort fehlt.');

$existingTeam = TeamRepository::get()->findByName($teamName);
if ($existingTeam) {
    // Vom Team ausgerichtete Turniere zuerst löschen, sonst schlägt das Löschen des Teams fehl.
    $turniere = DoctrineWrapper::manager()->getRepository(Turnier::class)->findBy(['ausrichter' => $existingTeam]);
    foreach ($turniere as $turnier) {
        TurnierRepository::get()->delete($turnier);
    }

    foreach ($existingTeam->getKader() as $spieler) {
        SpielerRepository::get()->delete($spieler);
    }
    TeamRepository::get()->delete($existingTeam);
}

$team = (new nTeam())
    ->setName($teamName)
    ->setLigateam('Ja')
    ->setAktiv('Ja')
    ->setPasswort($teamPasswort)
    ->setPasswortGeaendert('Ja'); // Sonst Redirect zu tc_pw_aendern.php
$team->setDetails(
    (new TeamDetails())
        ->setTeam($team)
        ->setLigavertreter('Playwright Bot'), // Sonst Redirect zu tc_teamdaten_aendern.php
);

TeamRepository::get()->speichern($team);

foreach (range(1, 5) as $i) {
    $spieler = (new Spieler())
        ->setVorname('Spieler')
        ->setNachname((string) $i)
        ->setJahrgang(2000)
        ->setGeschlecht('d')
        ->setTeam($team)
        ->setTimestamp(new DateTime())
        ->setLetzteSaison(Config::SAISON);
    SpielerRepository::get()->speichern($spieler);
}

echo json_encode([
    'teamName' => $teamName,
    'teamId' => $team->id(),
]) . \PHP_EOL;
