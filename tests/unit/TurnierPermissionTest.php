<?php

namespace unit;

use App\Team\Entity\Team;
use App\Turnier\Entity\Turnier;
use App\Turnier\Entity\Turnierdetails;
use App\Turnier\Enum\TurnierPhase;
use App\Turnier\Service\TurnierPermission;
use DateTime;
use DateTimeImmutable;
use Helper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TurnierPermissionTest extends TestCase
{
    private const AUSRICHTER_ID = 42;

    private bool $ligacenterVorher;
    private bool $teamcenterVorher;
    private mixed $sessionVorher;

    protected function setUp(): void
    {
        $this->ligacenterVorher = Helper::$ligacenter;
        $this->teamcenterVorher = Helper::$teamcenter;
        $this->sessionVorher = $_SESSION ?? null;

        Helper::$ligacenter = false;
        Helper::$teamcenter = false;
        $_SESSION['logins']['team']['id'] = 0;
    }

    protected function tearDown(): void
    {
        Helper::$ligacenter = $this->ligacenterVorher;
        Helper::$teamcenter = $this->teamcenterVorher;
        $_SESSION = $this->sessionVorher;
    }

    private function turnier(TurnierPhase $phase, string $art = 'II', int $plaetze = 6): Turnier
    {
        $ausrichter = $this->createStub(Team::class);
        $ausrichter->method('id')->willReturn(self::AUSRICHTER_ID);

        $turnier = (new Turnier())
            ->setArt($art)
            ->setBlock('BCD')
            ->setPhase($phase)
            ->setDatum(new DateTime('2026-05-02')) // Samstag
            ->setAusrichter($ausrichter);
        $turnier->setDetails((new Turnierdetails())->setTurnier($turnier));
        $turnier->getDetails()->setPlaetze($plaetze);

        return $turnier;
    }

    // ------------------------------------------------------------------
    // Turnierarten anlegen
    // ------------------------------------------------------------------

    public static function provideArten(): array
    {
        return [
            "I" => ["I", true, true],
            "II" => ["II", true, true],
            "III" => ["III", false, false],
            "final" => ["final", false, true],
            "fixed" => ["fixed", false, true],
            "spass" => ["spass", true, true],
            "unbekannt" => ["IV", false, false],
            "ohne Art" => [null, false, false],
        ];
    }

    #[DataProvider("provideArten")]
    public function testTeamDarfArtAnlegen(?string $art, bool $team, bool $la): void
    {
        Helper::$ligacenter = false;

        $this->assertSame(expected: $team, actual: TurnierPermission::canCreateArt($art));
    }

    #[DataProvider("provideArten")]
    public function testLaDarfArtAnlegen(?string $art, bool $team, bool $la): void
    {
        Helper::$ligacenter = true;

        $this->assertSame(expected: $la, actual: TurnierPermission::canCreateArt($art));
    }

    // ------------------------------------------------------------------
    // Rollen
    // ------------------------------------------------------------------

    public function testLaRechteImLigacenter(): void
    {
        Helper::$ligacenter = true;

        $this->assertTrue(TurnierPermission::hasLaRights($this->turnier(TurnierPhase::SETZ)));
    }

    public function testLaRechteBeiSpassturnierenNurFuerAusrichter(): void
    {
        Helper::$teamcenter = true;
        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID;
        $this->assertTrue(TurnierPermission::hasLaRights($this->turnier(TurnierPhase::WARTE, 'spass')));

        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID + 1;
        $this->assertFalse(TurnierPermission::hasLaRights($this->turnier(TurnierPhase::WARTE, 'spass')));
    }

    public function testLaRechteFuerAusrichterEinesFinalturniers(): void
    {
        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID;
        $this->assertTrue(TurnierPermission::hasLaRights($this->turnier(TurnierPhase::SETZ, 'final')));

        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID + 1;
        $this->assertFalse(TurnierPermission::hasLaRights($this->turnier(TurnierPhase::SETZ, 'final')));
    }

    public function testKeineLaRechteFuerTeamsBeiLigaturnieren(): void
    {
        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID;

        $this->assertFalse(TurnierPermission::hasLaRights($this->turnier(TurnierPhase::SETZ, 'II')));
    }

    // ------------------------------------------------------------------
    // Turnier bearbeiten
    // ------------------------------------------------------------------

    public static function providePhasen(): array
    {
        return [
            "warte" => [TurnierPhase::WARTE],
            "setz" => [TurnierPhase::SETZ],
            "spielplan" => [TurnierPhase::SPIELPLAN],
            "ergebnis" => [TurnierPhase::ERGEBNIS],
        ];
    }

    #[DataProvider("providePhasen")]
    public function testTeamDarfNurVorDerErgebnisphaseAendern(TurnierPhase $phase): void
    {
        $this->assertSame(
            expected: $phase !== TurnierPhase::ERGEBNIS,
            actual: TurnierPermission::canChange($this->turnier($phase)),
        );
    }

    #[DataProvider("providePhasen")]
    public function testLaDarfImmerAendern(TurnierPhase $phase): void
    {
        Helper::$ligacenter = true;

        $this->assertTrue(TurnierPermission::canChange($this->turnier($phase)));
    }

    /**
     * Feld => Phasen, in denen ein Team das Feld noch ändern darf.
     */
    public static function provideFelder(): array
    {
        $alle = [TurnierPhase::WARTE, TurnierPhase::SETZ, TurnierPhase::SPIELPLAN, TurnierPhase::ERGEBNIS];
        $faelle = [];
        $freigaben = [
            'datum' => [],
            'art_block' => [],
            'startzeit' => [TurnierPhase::WARTE, TurnierPhase::SETZ],
            'besprechung' => [TurnierPhase::WARTE, TurnierPhase::SETZ],
            'startgebuehr' => [TurnierPhase::WARTE],
            'sofort_oeffnen' => [TurnierPhase::WARTE],
            'plaetze' => [TurnierPhase::WARTE],
            'min_teams' => [TurnierPhase::WARTE],
            'hallenname' => $alle, // nicht gesperrte Felder
        ];
        foreach ($freigaben as $feld => $erlaubt) {
            foreach ($alle as $phase) {
                $faelle["$feld in {$phase->value}"] = [$feld, $phase, \in_array($phase, $erlaubt, true)];
            }
        }
        return $faelle;
    }

    #[DataProvider("provideFelder")]
    public function testTeamDarfFeldBearbeiten(string $feld, TurnierPhase $phase, bool $erlaubt): void
    {
        $this->assertSame(expected: $erlaubt, actual: TurnierPermission::canEditField($feld, $this->turnier($phase)));
    }

    #[DataProvider("provideFelder")]
    public function testLaDarfJedesFeldBearbeiten(string $feld, TurnierPhase $phase, bool $erlaubt): void
    {
        Helper::$ligacenter = true;

        $this->assertTrue(TurnierPermission::canEditField($feld, $this->turnier($phase)));
    }

    public function testBeimAnlegenSindAlleFelderFrei(): void
    {
        $this->assertTrue(TurnierPermission::canEditField('datum', null));
    }

    public static function providePlaetze(): array
    {
        return [
            "warte: erhöhen" => [TurnierPhase::WARTE, 6, 8, true],
            "setz: erhöhen" => [TurnierPhase::SETZ, 6, 8, false],
            "setz: gleich" => [TurnierPhase::SETZ, 6, 6, true],
            "setz: verringern" => [TurnierPhase::SETZ, 8, 6, true],
            "spielplan: erhöhen" => [TurnierPhase::SPIELPLAN, 6, 8, true],
        ];
    }

    #[DataProvider("providePlaetze")]
    public function testTeamDarfPlaetzeAbSetzphaseNichtErhoehen(TurnierPhase $phase, int $vorher, int $nachher, bool $erlaubt): void
    {
        $this->assertSame(
            expected: $erlaubt,
            actual: TurnierPermission::canChangePlaetze($this->turnier($phase, plaetze: $nachher), $vorher),
        );
    }

    public function testLaDarfPlaetzeImmerErhoehen(): void
    {
        Helper::$ligacenter = true;

        $this->assertTrue(TurnierPermission::canChangePlaetze($this->turnier(TurnierPhase::SETZ, plaetze: 8), 6));
    }

    // ------------------------------------------------------------------
    // Anmeldung, Blockerweiterung, Spielplan
    // ------------------------------------------------------------------

    #[DataProvider("providePhasen")]
    public function testAnmeldungNurBisZurSetzphase(TurnierPhase $phase): void
    {
        $this->assertSame(
            expected: \in_array($phase, [TurnierPhase::WARTE, TurnierPhase::SETZ], true),
            actual: TurnierPermission::canRegisterTeam($this->turnier($phase)),
        );
    }

    #[DataProvider("providePhasen")]
    public function testBlockerweiterungNurInDerSetzphase(TurnierPhase $phase): void
    {
        $this->assertSame(
            expected: $phase === TurnierPhase::SETZ,
            actual: TurnierPermission::canExtendBlock($this->turnier($phase)),
        );
    }

    #[DataProvider("providePhasen")]
    public function testSpielplanFuerLigaturniereNurInDerSetzphase(TurnierPhase $phase): void
    {
        $this->assertSame(
            expected: $phase === TurnierPhase::SETZ,
            actual: TurnierPermission::canCreateSpielplan($this->turnier($phase, 'II')),
        );
    }

    #[DataProvider("providePhasen")]
    public function testSpielplanFuerAndereTurniereInJederPhase(TurnierPhase $phase): void
    {
        $this->assertTrue(TurnierPermission::canCreateSpielplan($this->turnier($phase, 'final')));
    }

    // ------------------------------------------------------------------
    // Abmelden, Nichtligateams, Teams ersetzen
    // ------------------------------------------------------------------

    #[DataProvider("providePhasen")]
    public function testVonDerWartelisteJederzeitAbmelden(TurnierPhase $phase): void
    {
        $this->assertTrue(TurnierPermission::canDeregisterTeam($this->turnier($phase), aufSetzliste: false));
    }

    #[DataProvider("providePhasen")]
    public function testVonDerSetzlisteNurInDerSetzphaseAbmelden(TurnierPhase $phase): void
    {
        // Vor der Abmeldefrist (Fr, 17.04.2026 23:59 für das Turnier am 02.05.2026)
        $this->assertSame(
            expected: $phase === TurnierPhase::SETZ,
            actual: TurnierPermission::canDeregisterTeam(
                $this->turnier($phase),
                aufSetzliste: true,
                jetzt: new DateTimeImmutable('2026-04-10 12:00'),
            ),
        );
    }

    public static function provideAbmeldeFrist(): array
    {
        return [
            "Freitag 23:59" => ['2026-04-17 23:59:00', true],
            "Samstag 00:00" => ['2026-04-18 00:00:00', false],
        ];
    }

    #[DataProvider("provideAbmeldeFrist")]
    public function testVonDerSetzlisteBisZurAbmeldefristAbmelden(string $jetzt, bool $erlaubt): void
    {
        $this->assertSame(
            expected: $erlaubt,
            actual: TurnierPermission::canDeregisterTeam(
                $this->turnier(TurnierPhase::SETZ),
                aufSetzliste: true,
                jetzt: new DateTimeImmutable($jetzt),
            ),
        );
    }

    #[DataProvider("providePhasen")]
    public function testManuellAbmeldenBisZurSpielplanphase(TurnierPhase $phase): void
    {
        $this->assertSame(
            expected: $phase !== TurnierPhase::ERGEBNIS,
            actual: TurnierPermission::canDeregisterTeamManually($this->turnier($phase)),
        );
    }

    public function testTeamsManuellVerwaltenNurLaUndAusrichterVonSpassturnieren(): void
    {
        Helper::$teamcenter = true;
        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID;
        $this->assertTrue(TurnierPermission::canManageTeams($this->turnier(TurnierPhase::SETZ, 'spass')));
        $this->assertFalse(TurnierPermission::canManageTeams($this->turnier(TurnierPhase::SETZ, 'II')));

        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID + 1;
        $this->assertFalse(TurnierPermission::canManageTeams($this->turnier(TurnierPhase::SETZ, 'spass')));

        Helper::$teamcenter = false;
        Helper::$ligacenter = true;
        $this->assertTrue(TurnierPermission::canManageTeams($this->turnier(TurnierPhase::SETZ, 'II')));
    }

    public function testNichtligateamsMeldenNurLaUndAusrichterAn(): void
    {
        Helper::$teamcenter = true;
        $turnier = $this->turnier(TurnierPhase::SETZ);

        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID;
        $this->assertTrue(TurnierPermission::canRegisterNlTeam($turnier));

        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID + 1;
        $this->assertFalse(TurnierPermission::canRegisterNlTeam($turnier));

        Helper::$teamcenter = false;
        Helper::$ligacenter = true;
        $this->assertTrue(TurnierPermission::canRegisterNlTeam($turnier));
    }

    #[DataProvider("providePhasen")]
    public function testNurDerLaErsetztTeamsInJederPhase(TurnierPhase $phase): void
    {
        Helper::$teamcenter = true;
        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID;
        $this->assertFalse(TurnierPermission::canReplaceTeam($this->turnier($phase)));

        Helper::$teamcenter = false;
        Helper::$ligacenter = true;
        $this->assertTrue(TurnierPermission::canReplaceTeam($this->turnier($phase)));
    }

    // ------------------------------------------------------------------
    // Ergebnisse eintragen
    // ------------------------------------------------------------------

    #[DataProvider("providePhasen")]
    public function testAusrichterTraegtErgebnisseNurInDerSpielplanphaseEin(TurnierPhase $phase): void
    {
        Helper::$teamcenter = true;
        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID;

        $this->assertSame(
            expected: $phase === TurnierPhase::SPIELPLAN,
            actual: TurnierPermission::canEnterErgebnisse($this->turnier($phase)),
        );
    }

    #[DataProvider("providePhasen")]
    public function testLaTraegtErgebnisseInJederPhaseEin(TurnierPhase $phase): void
    {
        Helper::$ligacenter = true;

        $this->assertTrue(TurnierPermission::canEnterErgebnisse($this->turnier($phase)));
    }

    public function testImTeamcenterDarfNurDerAusrichterErgebnisseEintragen(): void
    {
        Helper::$teamcenter = true;
        $turnier = $this->turnier(TurnierPhase::SPIELPLAN);

        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID;
        $this->assertTrue(TurnierPermission::canEnterErgebnisse($turnier));

        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID + 1;
        $this->assertFalse(TurnierPermission::canEnterErgebnisse($turnier));
    }

    public function testLaDarfImmerErgebnisseEintragen(): void
    {
        Helper::$ligacenter = true;
        Helper::$teamcenter = true;
        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID + 1;

        $this->assertTrue(TurnierPermission::canEnterErgebnisse($this->turnier(TurnierPhase::SPIELPLAN)));
    }

    public static function provideErgebnisFrist(): array
    {
        // Turnier am Samstag, 02.05.2026 -> Frist Dienstag, 05.05.2026, 23:59 Uhr
        return [
            "Turniertag" => ['2026-05-02 16:00', true],
            "Montag 18:01" => ['2026-05-04 18:01', true],
            "Dienstag 23:59" => ['2026-05-05 23:59:59', true],
            "Mittwoch 00:00" => ['2026-05-06 00:00:00', false],
        ];
    }

    #[DataProvider("provideErgebnisFrist")]
    public function testErgebnisFristBisDienstagAbend(string $jetzt, bool $offen): void
    {
        $this->assertSame(
            expected: $offen,
            actual: TurnierPermission::isErgebnisFristOffen($this->turnier(TurnierPhase::SPIELPLAN), new DateTimeImmutable($jetzt)),
        );
    }

    public function testLaHatKeineErgebnisFrist(): void
    {
        Helper::$ligacenter = true;

        $this->assertTrue(
            TurnierPermission::isErgebnisFristOffen($this->turnier(TurnierPhase::ERGEBNIS), new DateTimeImmutable('2027-01-01')),
        );
    }

    // ------------------------------------------------------------------
    // Spaßturniere: volle Rechte für Ausrichter und Ligaausschuss
    // ------------------------------------------------------------------

    private function alsAusrichter(): void
    {
        Helper::$teamcenter = true;
        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID;
    }

    private function alsAnderesTeam(): void
    {
        Helper::$teamcenter = true;
        $_SESSION['logins']['team']['id'] = self::AUSRICHTER_ID + 1;
    }

    #[DataProvider("providePhasen")]
    public function testAusrichterVerwaltetSpassturnierInJederPhase(TurnierPhase $phase): void
    {
        $this->alsAusrichter();
        $turnier = $this->turnier($phase, 'spass');

        $this->assertTrue(TurnierPermission::canChange($turnier));
        $this->assertTrue(TurnierPermission::canManageTeams($turnier));
        $this->assertTrue(TurnierPermission::canRegisterTeam($turnier));
        $this->assertTrue(TurnierPermission::canRegisterNlTeam($turnier));
        $this->assertTrue(TurnierPermission::canDeregisterTeamManually($turnier));
        $this->assertTrue(TurnierPermission::canManageSpielplan($turnier));
        $this->assertTrue(TurnierPermission::canCreateSpielplan($turnier));
        $this->assertTrue(TurnierPermission::canEnterErgebnisse($turnier));
        $this->assertTrue(TurnierPermission::isErgebnisFristOffen($turnier, new DateTimeImmutable('2027-01-01')));
        $this->assertTrue(TurnierPermission::canChangePlaetze($turnier, 4));
    }

    #[DataProvider("providePhasen")]
    public function testAusrichterBearbeitetAlleFelderAusserDerArt(TurnierPhase $phase): void
    {
        $this->alsAusrichter();
        $turnier = $this->turnier($phase, 'spass');

        foreach (['datum', 'startzeit', 'besprechung', 'startgebuehr', 'plaetze', 'min_teams'] as $feld) {
            $this->assertTrue(TurnierPermission::canEditField($feld, $turnier), $feld);
        }
        $this->assertFalse(TurnierPermission::canEditField('art_block', $turnier), "Die Turnierart bleibt für Teams gesperrt");
    }

    public function testAndereTeamsVerwaltenKeineSpassturniere(): void
    {
        $this->alsAnderesTeam();
        $turnier = $this->turnier(TurnierPhase::WARTE, 'spass');

        $this->assertFalse(TurnierPermission::hasLaRights($turnier));
        $this->assertFalse(TurnierPermission::canManageTeams($turnier));
        $this->assertFalse(TurnierPermission::canRegisterNlTeam($turnier));
        $this->assertFalse(TurnierPermission::canManageSpielplan($turnier));
        $this->assertFalse(TurnierPermission::canEnterErgebnisse($turnier));
    }

    public function testLaVerwaltetSpassturnierInDerErgebnisphase(): void
    {
        Helper::$ligacenter = true;
        $turnier = $this->turnier(TurnierPhase::ERGEBNIS, 'spass');

        $this->assertTrue(TurnierPermission::canDeregisterTeamManually($turnier));
        $this->assertTrue(TurnierPermission::canRegisterTeam($turnier));
        $this->assertTrue(TurnierPermission::canManageSpielplan($turnier));
    }

    public function testAusrichterEinesLigaturniersVerwaltetKeinenSpielplan(): void
    {
        $this->alsAusrichter();

        $this->assertFalse(TurnierPermission::canManageSpielplan($this->turnier(TurnierPhase::SETZ)));
    }

    public function testAbsagenNurLaUndAusrichterVonSpassturnieren(): void
    {
        $this->alsAusrichter();
        $this->assertTrue(TurnierPermission::canCancel($this->turnier(TurnierPhase::WARTE, 'spass')));
        $this->assertFalse(TurnierPermission::canCancel($this->turnier(TurnierPhase::WARTE)), "Ligaturnier");

        $this->alsAnderesTeam();
        $this->assertFalse(TurnierPermission::canCancel($this->turnier(TurnierPhase::WARTE, 'spass')));

        Helper::$teamcenter = false;
        Helper::$ligacenter = true;
        $this->assertTrue(TurnierPermission::canCancel($this->turnier(TurnierPhase::ERGEBNIS)));
    }
}
