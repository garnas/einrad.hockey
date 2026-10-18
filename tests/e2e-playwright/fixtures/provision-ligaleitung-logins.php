<?php

/**
 * Legt (bzw. erneuert) die Fixtures für die Login- und Passwort-Tests der Ligaleitung an
 * (login.php, lc_pw_aendern.php, oc_pw_aendern.php).
 * Wird vor dem Testlauf per CLI ausgeführt (siehe global-setup.ts).
 *
 * Legt an:
 * - ein Team mit drei Spielern, denen die Ligaleitungs-Logins zugeordnet sind
 * - je einen Ligaausschuss- und Öffentlichkeitsausschuss-Login für die Login-Tests
 * - je einen Ligaausschuss- und Öffentlichkeitsausschuss-Login für die Passwort-Änderung,
 *   damit ein geändertes Passwort die übrigen Tests nicht beeinflusst
 * - einen Login mit einer Funktion ohne eigenen Login-Bereich (schiriausbilder)
 */

use App\Ligaleitung\Entity\Ligaleitung;
use App\Ligaleitung\Repository\LigaleitungRepository;
use App\Shared\DoctrineWrapper;
use App\Spieler\Entity\Spieler;
use App\Spieler\Repository\SpielerRepository;
use App\Team\Entity\Team;
use App\Team\Entity\Teamdetails;
use App\Team\Repository\TeamRepository;

require_once __DIR__ . '/../../../init.php';

$passwort = $argv[1] ?? throw new InvalidArgumentException('Passwort fehlt.');

// Müssen zu team.ts passen
$teamName = 'Playwright-Ligaleitung-Team';
$logins = [
    // login => [funktion, vorname]
    'playwright-la-login' => ['ligaausschuss', 'Lara'],
    'playwright-la-pw' => ['ligaausschuss', 'Lara'],
    'playwright-oa-login' => ['team_social_media', 'Olga'],
    'playwright-oa-pw' => ['team_social_media', 'Olga'],
    'playwright-schiriausbilder' => ['schiriausbilder', 'Sven'],
];

$manager = DoctrineWrapper::manager();

// -------------------------------------------------------------------------
//  Aufräumen (Logins zuerst, danach Team samt Kader)
// -------------------------------------------------------------------------
foreach (array_keys($logins) as $login) {
    $bestehend = LigaleitungRepository::get()->findByLogin($login);
    if ($bestehend !== null) {
        $manager->remove($bestehend);
        $manager->flush();
    }
}

$team = TeamRepository::get()->findByName($teamName);
if ($team) {
    foreach ($team->getKader() as $spieler) {
        SpielerRepository::get()->delete($spieler);
    }
    TeamRepository::get()->delete($team);
}

// -------------------------------------------------------------------------
//  Team und Logins
// -------------------------------------------------------------------------
$team = (new Team())
    ->setName($teamName)
    ->setLigateam('Ja')
    ->setAktiv('Ja')
    ->setPasswort('nicht-eingeloggt')
    ->setPasswortGeaendert('Ja');
$team->setDetails(
    (new Teamdetails())
        ->setTeam($team)
        ->setLigavertreter('Playwright Bot')
        ->setTeamfoto(null)
        ->setTrikotFarbe1(null)
        ->setTrikotFarbe2(null),
);
TeamRepository::get()->speichern($team);

$spielerNachVorname = [];
foreach ($logins as $login => [$funktion, $vorname]) {
    $spieler = $spielerNachVorname[$vorname] ??= (new Spieler())
        ->setVorname($vorname)
        ->setNachname('Playwright')
        ->setJahrgang(1990)
        ->setGeschlecht('d')
        ->setTeam($team)
        ->setTimestamp(new DateTime())
        ->setLetzteSaison(Config::SAISON);
    SpielerRepository::get()->speichern($spieler);

    $ligaleitung = (new Ligaleitung())
        ->setFunktion($funktion)
        ->setEmail($login . '@playwright-test.de')
        ->setLogin($login)
        ->setPasswort(password_hash($passwort, \PASSWORD_DEFAULT))
        ->setSpieler($spieler);
    LigaleitungRepository::get()->speichern($ligaleitung);
}

echo json_encode(['teamName' => $teamName, 'logins' => array_keys($logins)]) . \PHP_EOL;
