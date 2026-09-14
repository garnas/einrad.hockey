<?php

/**
 * Legt (bzw. erneuert) die Fixtures für die Spielplan-Tests im Ligacenter an
 * (lc_spielplan_verwalten.php + lc_spielplan.php).
 * Wird vor dem Testlauf per CLI ausgeführt (siehe global-setup.ts).
 *
 * Legt an:
 * - einen Ligaausschuss-Login (ligaleitung + zugehöriger Spieler + Team)
 * - vier Ligateams für die Setz-/Spielenliste
 * - ein Ligaturnier in der Setzphase ohne Spielplan  -> "Spielplan erstellen"-Test
 * - ein Ligaturnier mit bereits erstelltem JgJ-Spielplan -> "Ergebnisse eintragen"-Test
 *
 * Die erzeugten Turnier-IDs werden nach fixtures/spielplan.fixture.json geschrieben,
 * damit der Test sie ohne Umweg über die Turnierliste ansteuern kann.
 */

use App\Entity\Team\Ligaleitung;
use App\Entity\Team\nTeam;
use App\Entity\Team\Spieler;
use App\Entity\Team\TeamDetails;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurnierDetails;
use App\Repository\DoctrineWrapper;
use App\Repository\Spielplan\SpielplanRepository;
use App\Repository\Spieler\SpielerRepository;
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use App\Entity\TurnierBericht\TurnierBericht;
use App\Repository\TurnierBericht\TurnierBerichtRepository;
use App\Service\Spielplan\SpielplanService;
use App\Service\Turnier\TurnierService;

require_once __DIR__ . '/../../../init.php';

$loginName = $argv[1] ?? throw new InvalidArgumentException('Loginname fehlt.');
$loginPasswort = $argv[2] ?? throw new InvalidArgumentException('Passwort fehlt.');

// Müssen zu spielplan.ts passen
$laTeamName = 'Playwright-Ligaausschuss-Team';
$turnierTeamNamen = ['Playwright SP Team 1', 'Playwright SP Team 2', 'Playwright SP Team 3', 'Playwright SP Team 4'];
$turnierNameSetz = 'Playwright Spielplan Turnier';
$turnierNameErgebnis = 'Playwright Ergebnis Turnier';

$manager = DoctrineWrapper::manager();

// -------------------------------------------------------------------------
//  Aufräumen (Turniere zuerst, danach Teams samt Kader)
// -------------------------------------------------------------------------
foreach ([$turnierNameSetz, $turnierNameErgebnis] as $turnierName) {
    foreach ($manager->getRepository(Turnier::class)->findBy(['name' => $turnierName]) as $turnier) {
        SpielplanRepository::get()->loescheSpiele($turnier);
        $bericht = TurnierBerichtRepository::get()->bericht($turnier->id());
        if ($bericht !== null) {
            TurnierBerichtRepository::get()->delete($bericht);
        }
        TurnierRepository::get()->delete($turnier);
    }
}

$loginBestehend = $manager->getRepository(Ligaleitung::class)->findOneBy(['login' => $loginName]);
if ($loginBestehend !== null) {
    $manager->remove($loginBestehend);
    $manager->flush();
}

foreach ([...$turnierTeamNamen, $laTeamName] as $teamName) {
    $team = TeamRepository::get()->findByName($teamName);
    if ($team) {
        foreach ($team->getKader() as $spieler) {
            SpielerRepository::get()->delete($spieler);
        }
        TeamRepository::get()->delete($team);
    }
}

// -------------------------------------------------------------------------
//  Ligaausschuss-Login
// -------------------------------------------------------------------------
$laTeam = (new nTeam())
    ->setName($laTeamName)
    ->setLigateam('Ja')
    ->setAktiv('Ja')
    ->setPasswort('nicht-eingeloggt')
    ->setPasswortGeaendert('Ja');
$laTeam->setDetails(
    (new TeamDetails())
        ->setTeam($laTeam)
        ->setLigavertreter('Playwright Bot')
        ->setTeamfoto(null)
        ->setTrikotFarbe1(null)
        ->setTrikotFarbe2(null),
);
TeamRepository::get()->speichern($laTeam);

$laSpieler = (new Spieler())
    ->setVorname('Playwright')
    ->setNachname('Ligaausschuss')
    ->setJahrgang(1990)
    ->setGeschlecht('d')
    ->setTeam($laTeam)
    ->setTimestamp(new DateTime())
    ->setLetzteSaison(Config::SAISON);
SpielerRepository::get()->speichern($laSpieler);

$ligaleitung = (new Ligaleitung())
    ->setFunktion('ligaausschuss')
    ->setEmail('playwright-la@playwright-test.de')
    ->setLogin($loginName)
    ->setPasswort(password_hash($loginPasswort, \PASSWORD_DEFAULT))
    ->setSpieler($laSpieler);
$manager->persist($ligaleitung);
$manager->flush();

// -------------------------------------------------------------------------
//  Teams für die Setzliste
// -------------------------------------------------------------------------
$teams = [];
foreach ($turnierTeamNamen as $teamName) {
    $team = (new nTeam())
        ->setName($teamName)
        ->setLigateam('Ja')
        ->setAktiv('Ja')
        ->setPasswort('nicht-eingeloggt')
        ->setPasswortGeaendert('Ja');
    $team->setDetails(
        (new TeamDetails())
            ->setTeam($team)
            ->setLigavertreter('Playwright Bot')
            ->setTeamfoto(null)
            ->setTrikotFarbe1(null)
            ->setTrikotFarbe2(null),
    );
    TeamRepository::get()->speichern($team);
    $teams[] = $team;
}

// -------------------------------------------------------------------------
//  Turniere
// -------------------------------------------------------------------------
$macheTurnier = static function (string $name) use ($teams): Turnier {
    // Spieltag 1: garantiert kein früheres offenes Turnier -> Ergebniseintragung immer möglich.
    $datum = new DateTime('saturday this week');

    $turnier = (new Turnier())
        ->setName($name)
        ->setArt('II')
        ->setAusrichter($teams[0])
        ->setBlock('CD')
        ->setBlockFixed('Nein')
        ->setDatum($datum)
        ->setDatumBis(null)
        ->setSpieltag(1)
        ->setSaison(Config::SAISON)
        ->setPhase('setz')
        ->setCanceled(false)
        ->setErstelltAm(new DateTime())
        ->setSofortOeffnenFrei(false)
        ->setSofortOeffnenHoch(false)
        ->setSofortOeffnenRunter(false)
        ->setBlockErweitertFrei(false)
        ->setBlockErweitertHoch(false)
        ->setBlockErweitertRunter(false);

    $details = (new TurnierDetails())
        ->setTurnier($turnier)
        ->setBesprechung('Nein')
        ->setHallenname('Playwright Sporthalle')
        ->setHaltestellen('')
        ->setHandy('0123456789')
        ->setOrganisator('Playwright Bot')
        ->setHinweis('')
        ->setPlz('12345')
        ->setOrt('Teststadt')
        ->setStrasse('Teststraße 1')
        ->setStartgebuehr('keine')
        ->setStartzeit(DateTime::createFromFormat('H:i', '10:00'))
        ->setPlaetze('4')
        ->setMinTeams(4);
    $turnier->setDetails($details);

    TurnierRepository::get()->speichern($turnier);

    foreach ($teams as $team) {
        TurnierService::addToSetzListe($turnier, $team);
    }
    TurnierRepository::get()->speichern($turnier);

    return $turnier;
};

$turnierIdSetz = $macheTurnier($turnierNameSetz)->id();
$turnierIdErgebnis = $macheTurnier($turnierNameErgebnis)->id();

// Turnier frisch aus der DB laden (wie im echten Request), damit alle nullbaren
// Felder hydriert sind, bevor der Spielplan erstellt wird.
$manager->clear();
$turnierErgebnis = TurnierRepository::get()->turnier($turnierIdErgebnis);
if (!SpielplanService::erstellen($turnierErgebnis)) {
    throw new RuntimeException('JgJ-Spielplan für das Ergebnis-Turnier konnte nicht erstellt werden.');
}

// Turnierbericht anlegen (im echten Ablauf passiert das beim Erstellen des Spielplans über
// lc_spielplan_verwalten.php). spielplan_form.logic.php erwartet einen vorhandenen Bericht.
if (TurnierBerichtRepository::get()->bericht($turnierIdErgebnis) === null) {
    TurnierBerichtRepository::get()->speichern(new TurnierBericht($turnierErgebnis));
}

// -------------------------------------------------------------------------
//  IDs für den Test hinterlegen
// -------------------------------------------------------------------------
$ausgabe = [
    'turnierIdSpielplan' => $turnierIdSetz,
    'turnierIdErgebnis' => $turnierIdErgebnis,
];
file_put_contents(__DIR__ . '/spielplan.fixture.json', json_encode($ausgabe, \JSON_PRETTY_PRINT) . \PHP_EOL);

echo json_encode($ausgabe) . \PHP_EOL;
