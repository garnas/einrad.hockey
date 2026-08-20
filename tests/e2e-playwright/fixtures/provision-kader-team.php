<?php

/**
 * Legt (bzw. erneuert) das Fixture-Team samt Testspielern für tc_kader.php an.
 * Wird vor dem Testlauf per CLI ausgeführt (siehe global-setup.ts).
 *
 * Legt zusätzlich an:
 * - einen eigenen Spieler mit letzteSaison = Vorsaison (für "Aus der Vorsaison übernehmen")
 * - ein zweites Team mit einem übernehmbaren Spieler (für "Von anderem Team übernehmen")
 */

use App\Entity\Team\nTeam;
use App\Entity\Team\Spieler;
use App\Entity\Team\TeamDetails;
use App\Repository\Spieler\SpielerRepository;
use App\Repository\Team\TeamRepository;

require_once __DIR__ . '/../../../init.php';

$teamName = $argv[1] ?? throw new InvalidArgumentException('Teamname fehlt.');
$teamPasswort = $argv[2] ?? throw new InvalidArgumentException('Passwort fehlt.');

// Müssen zu team.ts passen
$geberTeamName = $teamName . ' Geberteam';
[$vorsaisonVorname, $vorsaisonNachname] = ['Vorsaison', 'Spielen'];
[$vorsaisonVornameOhneDsgvo, $vorsaisonNachnameOhneDsgvo] = ['NoSaison', 'Spielerin'];
[$uebernahmeVorname, $uebernahmeNachname] = ['Wechsel', 'Spieler'];

foreach ([$teamName, $geberTeamName] as $existingTeamName) {
    $existingTeam = TeamRepository::get()->findByName($existingTeamName);
    if ($existingTeam) {
        foreach ($existingTeam->getKader() as $spieler) {
            SpielerRepository::get()->delete($spieler);
        }
        TeamRepository::get()->delete($existingTeam);

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

$geberTeam = (new nTeam())
    ->setName($geberTeamName)
    ->setLigateam('Ja')
    ->setAktiv('Ja')
    ->setPasswort('nicht-eingeloggt')
    ->setPasswortGeaendert('Ja');
$geberTeam->setDetails(
    (new TeamDetails())
        ->setTeam($geberTeam)
        ->setLigavertreter('Playwright Bot'),
);
TeamRepository::get()->speichern($geberTeam);

$vorsaisonSpieler = (new Spieler())
    ->setVorname($vorsaisonVorname)
    ->setNachname($vorsaisonNachname)
    ->setJahrgang(2000)
    ->setGeschlecht('d')
    ->setTeam($team)
    ->setTimestamp(new DateTime())
    ->setLetzteSaison(Config::SAISON - 1);
SpielerRepository::get()->speichern($vorsaisonSpieler);

$vorsaisonSpielerOhneDsgvo = (new Spieler())
    ->setVorname($vorsaisonVornameOhneDsgvo)
    ->setNachname($vorsaisonNachnameOhneDsgvo)
    ->setJahrgang(2000)
    ->setGeschlecht('w')
    ->setTeam($team)
    ->setTimestamp(new DateTime())
    ->setLetzteSaison(Config::SAISON - 1);
SpielerRepository::get()->speichern($vorsaisonSpielerOhneDsgvo);

$uebernahmeSpieler = (new Spieler())
    ->setVorname($uebernahmeVorname)
    ->setNachname($uebernahmeNachname)
    ->setJahrgang(1999)
    ->setGeschlecht('d')
    ->setTeam($geberTeam)
    ->setTimestamp(new DateTime())
    ->setLetzteSaison(Config::SAISON - 1);
SpielerRepository::get()->speichern($uebernahmeSpieler);

echo json_encode([
    'teamName' => $teamName,
    'teamId' => $team->id(),
    'geberTeamName' => $geberTeamName,
    'vorsaisonSpielerName' => $vorsaisonVorname . ' ' . $vorsaisonNachname,
    'vorsaisonSpielerNameOhneDsgvo' => $vorsaisonVornameOhneDsgvo . ' ' . $vorsaisonVornameOhneDsgvo,
    'uebernahmeSpielerName' => $uebernahmeVorname . ' ' . $uebernahmeNachname,
]) . \PHP_EOL;
