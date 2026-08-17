<?php

/**
 * Legt (bzw. erneuert) das Test-Team für die Playwright-Tests von tc_kader.php an.
 * Wird vor dem Testlauf per CLI ausgeführt (siehe global-setup.ts).
 */

use App\Entity\Team\nTeam;
use App\Entity\Team\TeamDetails;
use App\Repository\Team\TeamRepository;

require_once __DIR__ . '/../../../init.php';

$teamName = $argv[1] ?? throw new InvalidArgumentException('Teamname fehlt.');
$teamPasswort = $argv[2] ?? throw new InvalidArgumentException('Passwort fehlt.');

$team = TeamRepository::get()->findByName($teamName);
if ($team) {
    TeamRepository::get()->delete($team);
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

echo json_encode(['teamName' => $teamName, 'teamId' => $team->id()]) . \PHP_EOL;
