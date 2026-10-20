<?php

namespace unit;

use App\Ligaleitung\Entity\Ligaleitung;
use App\Team\Entity\Team;
use App\Spieler\Entity\Spieler;
use App\Ligaleitung\Repository\LigaleitungRepository;
use App\Ligaleitung\Service\LigaleitungService;
use Helper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use ReflectionProperty;

class LigaleitungServiceTest extends TestCase
{
    private const string PASSWORT = 'richtiges-passwort';

    private bool $logUser;

    protected function setUp(): void
    {
        $_SESSION = [];
        // Keine Einträge ins Login-Log schreiben
        $this->logUser = Helper::$log_user;
        Helper::$log_user = false;
    }

    protected function tearDown(): void
    {
        self::setRepository(null);
        $_SESSION = [];
        Helper::$log_user = $this->logUser;
    }

    private static function setRepository(?LigaleitungRepository $repository): void
    {
        (new ReflectionProperty(LigaleitungRepository::class, 'instance'))->setValue(null, $repository);
    }

    /**
     * Stellt ein Repository bereit, das zu jedem Login die übergebene Ligaleitung findet (oder null)
     */
    private function repositoryMit(?Ligaleitung $ligaleitung): void
    {
        $repository = $this->createStub(LigaleitungRepository::class);
        $repository->method('findByLogin')->willReturnCallback(
            static fn(string $login) => $ligaleitung?->getLogin() === $login ? $ligaleitung : null,
        );
        self::setRepository($repository);
    }

    private static function ligaleitung(
        string $funktion,
        string $login = 'max',
        ?string $passwort = self::PASSWORT,
        int $id = 42,
    ): Ligaleitung {
        $ligaleitung = (new Ligaleitung())
            ->setFunktion($funktion)
            ->setLogin($login)
            ->setPasswort($passwort === null ? null : password_hash($passwort, \PASSWORD_DEFAULT));
        (new ReflectionProperty(Ligaleitung::class, 'ligaleitungId'))->setValue($ligaleitung, $id);
        return $ligaleitung;
    }

    private static function fehlermeldungen(): array
    {
        return array_column(
            array_filter($_SESSION['messages'] ?? [], static fn(array $m) => $m['type'] === 'error'),
            'text',
        );
    }

    // -------------------------------------------------------------------------
    //  login
    // -------------------------------------------------------------------------

    public static function provideLoginKontexte(): array
    {
        return [
            'Ligaausschuss' => ['ligaausschuss', 'ligaausschuss', 'la'],
            'Admin im Ligacenter' => ['admin', 'ligaausschuss', 'la'],
            'Öffentlichkeitsausschuss' => ['team_social_media', 'team_social_media', 'oa'],
            'Schiriausschuss' => ['schiriausschuss', 'schiriausschuss', 'sa'],
        ];
    }

    #[DataProvider('provideLoginKontexte')]
    public function testLoginErfolgreich(string $funktionDb, string $funktionLogin, string $kontext): void
    {
        $this->repositoryMit(self::ligaleitung($funktionDb));

        $this->assertTrue(LigaleitungService::login('max', self::PASSWORT, $funktionLogin));
        $this->assertSame(['id' => 42, 'login' => 'max'], $_SESSION['logins'][$kontext]);
        $this->assertSame([], self::fehlermeldungen());
    }

    public function testLoginUnbekannterLoginname(): void
    {
        $this->repositoryMit(null);

        $this->assertFalse(LigaleitungService::login('unbekannt', self::PASSWORT, 'ligaausschuss'));
        $this->assertArrayNotHasKey('logins', $_SESSION);
        $this->assertSame(['Unbekannter Loginname'], self::fehlermeldungen());
    }

    public function testLoginFalschesPasswort(): void
    {
        $this->repositoryMit(self::ligaleitung('ligaausschuss'));

        $this->assertFalse(LigaleitungService::login('max', 'falsch', 'ligaausschuss'));
        $this->assertArrayNotHasKey('logins', $_SESSION);
        $this->assertSame(['Falsches Passwort'], self::fehlermeldungen());
    }

    public function testLoginOhneGespeichertesPasswortSchlaegtFehl(): void
    {
        $this->repositoryMit(self::ligaleitung('ligaausschuss', passwort: null));

        $this->assertFalse(LigaleitungService::login('max', '', 'ligaausschuss'));
        $this->assertArrayNotHasKey('logins', $_SESSION);
    }

    public static function provideFremdeKontexte(): array
    {
        return [
            'Öffentlichkeitsausschuss im Ligacenter' => ['team_social_media', 'ligaausschuss'],
            'Ligaausschuss im Öffentlichkeitscenter' => ['ligaausschuss', 'team_social_media'],
            'Schiriausschuss im Ligacenter' => ['schiriausschuss', 'ligaausschuss'],
            'Ligaausschuss im Schiricenter' => ['ligaausschuss', 'schiriausschuss'],
            'Schiriausbilder im Schiricenter' => ['schiriausbilder', 'schiriausschuss'],
            'Funktion ohne Login-Bereich' => ['schiriausbilder', 'ligaausschuss'],
            'unbekannter Login-Bereich' => ['ligaausschuss', 'schiriausbilder'],
        ];
    }

    /**
     * login.php probiert nacheinander alle Bereiche durch, daher darf ein Login im falschen Bereich
     * keine Fehlermeldung erzeugen.
     */
    #[DataProvider('provideFremdeKontexte')]
    public function testLoginImFremdenKontextSchlaegtStillFehl(string $funktionDb, string $funktionLogin): void
    {
        $this->repositoryMit(self::ligaleitung($funktionDb));

        $this->assertFalse(LigaleitungService::login('max', self::PASSWORT, $funktionLogin));
        $this->assertArrayNotHasKey('logins', $_SESSION);
        $this->assertSame([], self::fehlermeldungen());
    }

    // -------------------------------------------------------------------------
    //  isLoggedIn / eingeloggt
    // -------------------------------------------------------------------------

    public function testIsLoggedIn(): void
    {
        $_SESSION['logins']['la'] = ['id' => 1, 'login' => 'max'];

        $this->assertTrue(LigaleitungService::isLoggedIn('ligaausschuss'));
        $this->assertTrue(LigaleitungService::isLoggedIn('admin'));
        $this->assertFalse(LigaleitungService::isLoggedIn('team_social_media'));
        $this->assertFalse(LigaleitungService::isLoggedIn('schiriausschuss'));
        $this->assertFalse(LigaleitungService::isLoggedIn('schiriausbilder'));
    }

    public function testIsLoggedInIgnoriertTeamLogin(): void
    {
        $_SESSION['logins']['team'] = ['id' => 1];

        $this->assertFalse(LigaleitungService::isLoggedIn('ligaausschuss'));
        $this->assertFalse(LigaleitungService::isLoggedIn('team_social_media'));
    }

    public function testEingeloggt(): void
    {
        $ligaleitung = self::ligaleitung('team_social_media');
        $this->repositoryMit($ligaleitung);

        $this->assertNull(LigaleitungService::getLoggedInLigaleitungByFunktion('team_social_media'));

        $_SESSION['logins']['oa'] = ['id' => 42, 'login' => 'max'];
        $this->assertSame($ligaleitung, LigaleitungService::getLoggedInLigaleitungByFunktion('team_social_media'));
        $this->assertNull(LigaleitungService::getLoggedInLigaleitungByFunktion('ligaausschuss'));
    }

    // -------------------------------------------------------------------------
    //  setPasswort / checkPasswort
    // -------------------------------------------------------------------------

    public function testSetPasswortErfolgreich(): void
    {
        $ligaleitung = self::ligaleitung('ligaausschuss');
        $repository = $this->createMock(LigaleitungRepository::class);
        $repository->method('findByLogin')->with('max')->willReturn($ligaleitung);
        $repository->expects($this->once())->method('speichern')->with($ligaleitung);
        self::setRepository($repository);

        $this->assertTrue(LigaleitungService::setPasswort('max', 'neues-passwort', self::PASSWORT));
        $this->assertTrue(password_verify('neues-passwort', $ligaleitung->getPasswort()));
        $this->assertFalse(password_verify(self::PASSWORT, $ligaleitung->getPasswort()));
    }

    public function testSetPasswortMitFalschemAltemPasswort(): void
    {
        $ligaleitung = self::ligaleitung('ligaausschuss');
        $hashVorher = $ligaleitung->getPasswort();
        $repository = $this->createMock(LigaleitungRepository::class);
        $repository->method('findByLogin')->willReturn($ligaleitung);
        $repository->expects($this->never())->method('speichern');
        self::setRepository($repository);

        $this->assertFalse(LigaleitungService::setPasswort('max', 'neues-passwort', 'falsch'));
        $this->assertSame($hashVorher, $ligaleitung->getPasswort());
    }

    public function testSetPasswortUnbekannterLogin(): void
    {
        $repository = $this->createMock(LigaleitungRepository::class);
        $repository->method('findByLogin')->willReturn(null);
        $repository->expects($this->never())->method('speichern');
        self::setRepository($repository);

        $this->assertFalse(LigaleitungService::setPasswort('unbekannt', 'neues-passwort', self::PASSWORT));
    }

    public function testCheckPasswort(): void
    {
        $this->repositoryMit(self::ligaleitung('ligaausschuss'));

        $this->assertTrue(LigaleitungService::checkPasswort('max', self::PASSWORT));
        $this->assertFalse(LigaleitungService::checkPasswort('max', 'falsch'));
        $this->assertFalse(LigaleitungService::checkPasswort('unbekannt', self::PASSWORT));
        $this->assertArrayNotHasKey('logins', $_SESSION);
    }

    // -------------------------------------------------------------------------
    //  getNameMitTeam
    // -------------------------------------------------------------------------

    public function testGetNameMitTeam(): void
    {
        $team = $this->createStub(Team::class);
        $team->method('getName')->willReturn('Testteam');
        $spieler = (new Spieler())->setVorname('Max')->setNachname('Mustermann')->setTeam($team);

        $this->assertSame(
            'Max Mustermann (Testteam)',
            LigaleitungService::getNameMitTeam(self::ligaleitung('ligaausschuss')->setSpieler($spieler)),
        );
    }

    public function testGetNameOhneTeam(): void
    {
        $spieler = (new Spieler())->setVorname('Max')->setNachname('Mustermann')->setTeam(null);

        $this->assertSame(
            'Max Mustermann',
            LigaleitungService::getNameMitTeam(self::ligaleitung('ligaausschuss')->setSpieler($spieler)),
        );
    }
}
