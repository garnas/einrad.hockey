<?php

/**
 * Legt (bzw. erneuert) die Fixture-Teams für tc_teamdaten_aendern.php an.
 * Wird vor dem Testlauf per CLI ausgeführt (siehe global-setup.ts).
 *
 * Legt zusätzlich an:
 * - ein zweites Team mit genau einer E-Mail-Adresse, um zu prüfen, dass die letzte
 *   E-Mail-Adresse eines Teams nicht gelöscht werden kann
 */

use App\Entity\Team\nTeam;
use App\Entity\Team\TeamDetails;
use App\Repository\Team\TeamRepository;

require_once __DIR__ . '/../../../init.php';

$teamName = $argv[1] ?? throw new InvalidArgumentException('Teamname fehlt.');
$teamPasswort = $argv[2] ?? throw new InvalidArgumentException('Passwort fehlt.');

// Muss zu team.ts passen
$soloTeamName = $teamName . ' Solo';

foreach ([$teamName, $soloTeamName] as $existingTeamName) {
    $existingTeam = TeamRepository::get()->findByName($existingTeamName);
    if ($existingTeam) {
        TeamRepository::get()->delete($existingTeam); // teams_kontakt wird per ON DELETE CASCADE mitgelöscht
    }
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

// Zwei E-Mail-Adressen, damit sowohl das Ändern der Sichtbarkeit als auch das Löschen
// einer von mehreren E-Mail-Adressen getestet werden kann.
$kontakte = new Kontakt($team->id());
$kontakte->set_email('team-a@playwright-test.de', 'Ja', 'Ja');
$kontakte->set_email('team-b@playwright-test.de', 'Nein', 'Nein');

$soloTeam = (new nTeam())
    ->setName($soloTeamName)
    ->setLigateam('Ja')
    ->setAktiv('Ja')
    ->setPasswort($teamPasswort)
    ->setPasswortGeaendert('Ja');
$soloTeam->setDetails(
    (new TeamDetails())
        ->setTeam($soloTeam)
        ->setLigavertreter('Playwright Bot'),
);
TeamRepository::get()->speichern($soloTeam);

$soloKontakte = new Kontakt($soloTeam->id());
$soloKontakte->set_email('solo@playwright-test.de', 'Ja', 'Ja');

echo json_encode([
    'teamName' => $teamName,
    'teamId' => $team->id(),
    'soloTeamName' => $soloTeamName,
    'soloTeamId' => $soloTeam->id(),
]) . \PHP_EOL;
