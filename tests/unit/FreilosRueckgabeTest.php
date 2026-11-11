<?php

namespace unit;

use App\Team\Entity\Freilos;
use App\Team\Entity\Freilosgrund;
use App\Team\Entity\Team;
use App\Team\Service\FreilosService;
use App\Turnier\Entity\Turnier;
use App\Turnier\Entity\Turnieranmeldung;
use App\Turnier\Service\TurnierService;
use DateTime;
use PHPUnit\Framework\TestCase;

/**
 * Bei einer Turnierabsage erhalten angemeldete Teams ihr gesetztes Freilos zurück:
 * Das gesetzte Freilos bleibt verbraucht, das Team erhält ein neues mit Verweis darauf.
 */
class FreilosRueckgabeTest extends TestCase
{
    private Turnier $turnier;

    protected function setUp(): void
    {
        $this->turnier = (new Turnier())->setDatum(new DateTime('2026-05-02'));
    }

    private function team(string $name): Team
    {
        return (new Team())->setName($name);
    }

    private function anmelden(Team $team): void
    {
        $anmeldung = (new Turnieranmeldung())
            ->setTeam($team)
            ->setTurnier($this->turnier)
            ->setListe('setzliste');
        $this->turnier->getListe()->add($anmeldung);
        $team->getTurniereListe()->add($anmeldung);
    }

    private function freilosSetzen(Team $team, int $saison = 32): Freilos
    {
        $team->addFreilos(Freilosgrund::SONSTIGES, saison: $saison);
        $freilos = $team->getFreilose()->last();
        $freilos->setzen($this->turnier);
        $this->turnier->getGesetzteFreilose()->add($freilos);
        return $freilos;
    }

    public function testAngemeldetesTeamErhaeltNeuesFreilos(): void
    {
        $team = $this->team('Angemeldet');
        $this->anmelden($team);
        $gesetzt = $this->freilosSetzen($team, saison: 31);

        FreilosService::handleTurnierAbsage($this->turnier);

        $this->assertCount(2, $team->getFreilose());
        $neu = $team->getFreilose()->last();
        $this->assertSame(Freilosgrund::TURNIER_ABGESAGT, $neu->getGrund());
        $this->assertSame($gesetzt, $neu->getVorherigesFreilos());
        $this->assertSame(31, $neu->getSaison(), "Gleiche Saison wie das verbrauchte Freilos");
        $this->assertFalse($neu->isGesetzt(), "Das neue Freilos ist offen");
        $this->assertTrue($gesetzt->isGesetzt(), "Das gesetzte Freilos bleibt verbraucht");
    }

    public function testAbgemeldetesTeamErhaeltKeinFreilos(): void
    {
        $team = $this->team('Abgemeldet');
        $this->freilosSetzen($team); // gesetzt, danach abgemeldet: keine Anmeldung mehr

        FreilosService::handleTurnierAbsage($this->turnier);

        $this->assertCount(1, $team->getFreilose());
    }

    public function testKeinZweitesFreilosBeiErneuterAbsage(): void
    {
        $team = $this->team('Doppelt');
        $this->anmelden($team);
        $this->freilosSetzen($team);

        FreilosService::handleTurnierAbsage($this->turnier);
        FreilosService::handleTurnierAbsage($this->turnier);

        $this->assertCount(2, $team->getFreilose());
    }

    public function testKeinFreilosWennBereitsRecycelt(): void
    {
        $team = $this->team('Recycelt');
        $this->anmelden($team);
        $gesetzt = $this->freilosSetzen($team);
        $team->addFreilos(Freilosgrund::FREILOS_GESETZT, vorherigesFreilos: $gesetzt);

        $this->assertTrue(FreilosService::hasErsatzfreilos($gesetzt));
        $this->assertSame([], FreilosService::getRueckgabeFreilose($this->turnier));
    }

    public function testNurTeamsMitGesetztemFreilos(): void
    {
        $mitFreilos = $this->team('Mit Freilos');
        $ohneFreilos = $this->team('Ohne Freilos');
        $this->anmelden($mitFreilos);
        $this->anmelden($ohneFreilos);
        $this->freilosSetzen($mitFreilos);

        FreilosService::handleTurnierAbsage($this->turnier);

        $this->assertCount(2, $mitFreilos->getFreilose());
        $this->assertCount(0, $ohneFreilos->getFreilose());
    }

    public function testAbsageGibtFreiloseZurueck(): void
    {
        $team = $this->team('Absage');
        $this->anmelden($team);
        $this->freilosSetzen($team);

        TurnierService::cancel($this->turnier, 'Zu wenige Teams');

        $this->assertTrue($this->turnier->isCanceled());
        $this->assertCount(2, $team->getFreilose());
    }
}
