<?php

namespace unit;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurnierDetails;
use App\Entity\Turnier\TurniereListe;
use App\Event\Turnier\nLigaBot;
use App\Service\Turnier\TurnierService;
use PHPUnit\Framework\TestCase;

class TurniereListeTest extends TestCase
{
    protected function provideTeamAnmeldungMock(Turnier $turnier, string $name, bool $ligateam, ?string $block, string $liste, ?int $positionWarteliste = null): int
    {
        $teamMock = $this->createMock(nTeam::class);
        $teamMock->method('getBlock')->willReturn($block);
        $teamMock->method('isLigaTeam')->willReturn($ligateam);
        $teamMock->method('getName')->willReturn($name);


        $anmeldung = new TurniereListe();
        $anmeldung
            ->setTeam($teamMock)
            ->setListe($liste)
            ->setPositionWarteliste($positionWarteliste);

        // Team-ID muss dem Index der Liste entsprechen!
        $id = $turnier->getListe()->count();
        $teamMock->method('id')->willReturn($id);

        $turnier->getListe()->add($anmeldung);
        
        return $id;
    }

    protected function provideTurnier(string $block, string $phase, int $plaetze): Turnier
    {
        $turnier = new Turnier();
        $turnier
            ->setBlock($block)
            ->setPhase($phase);
        $turnier->setDetails((new TurnierDetails())->setTurnier($turnier));
        $turnier->getDetails()
            ->setPlaetze($plaetze);
        return $turnier;
    }

    public function testLosen(): void
    {
        // Turnier mit Anmeldungen erstellen
        $turnier = self::provideTurnier(block: "CD", phase: "setz", plaetze: 7);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "ausrichterCD", ligateam: true, block: "CD", liste: "setzliste");
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamAB", ligateam: true, block: "AB", liste: "warteliste");
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamBC", ligateam: true, block: "BC", liste: "warteliste");
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamCD", ligateam: true, block: "CD", liste: "warteliste");
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamDE", ligateam: true, block: "DE", liste: "warteliste");
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamEF", ligateam: true, block: "EF", liste: "warteliste");
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamF", ligateam: true, block: "F", liste: "warteliste");
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamNL_1", ligateam: false, block: null, liste: "warteliste");
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamNL_2", ligateam: false, block: null, liste: "warteliste");

        // Testdurchführung
        nLigaBot::losen($turnier);

        // Validiere Setzliste
        $teamnamenSetzliste = [];
        $setzliste = TurnierService::getSetzListe($turnier);
        foreach ($setzliste as $anmeldung) {
            $teamnamenSetzliste[] = $anmeldung->getTeam()->getName();
        }
        $this->assertContains(needle: "ausrichterCD", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamBC", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamCD", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamDE", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamNL_1", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamNL_2", haystack: $teamnamenSetzliste);

        // Validiere Warteliste
        $teamnamenWarteliste = [];
        $warteliste = TurnierService::getWarteliste($turnier);
        foreach ($warteliste as $anmeldung) {
            $teamnamenWarteliste[] = $anmeldung->getTeam()->getName();
            $this->assertContains(needle: $anmeldung->getPositionWarteliste(), haystack: [1, 2, 3]);
        }
        $this->assertContains(needle: "teamAB", haystack: $teamnamenWarteliste);
        $this->assertContains(needle: "teamEF", haystack: $teamnamenWarteliste);
        $this->assertContains(needle: "teamF", haystack: $teamnamenWarteliste);
    }

    public function testSetzlisteAuffuellen(): void
    {
        // Turnier mit Anmeldungen erstellen
        $turnier = self::provideTurnier(block: "CD", phase: "setz", plaetze: 7);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "ausrichterCD", ligateam: true, block: "CD", liste: "setzliste", positionWarteliste: null);
        $idAB = $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamAB", ligateam: true, block: "AB", liste: "warteliste", positionWarteliste: 1);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamBC", ligateam: true, block: "BC", liste: "warteliste", positionWarteliste: 2);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamCD", ligateam: true, block: "CD", liste: "warteliste", positionWarteliste: 3);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamDE", ligateam: true, block: "DE", liste: "warteliste", positionWarteliste: 4);
        $idEF = $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamEF", ligateam: true, block: "EF", liste: "warteliste", positionWarteliste: 5);
        $idF = $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamF", ligateam: true, block: "F", liste: "warteliste", positionWarteliste: 6);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamNL_1", ligateam: false, block: null, liste: "warteliste", positionWarteliste: 7);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamNL_2", ligateam: false, block: null, liste: "warteliste", positionWarteliste: 8);

        // Testdurchführung
        TurnierService::setzListeAuffuellen($turnier, false);
        TurnierService::neueWartelistePositionen($turnier);

        // Validiere Setzliste
        $teamnamenSetzliste = [];
        $setzliste = TurnierService::getSetzListe($turnier);
        foreach ($setzliste as $anmeldung) {
            $teamnamenSetzliste[] = $anmeldung->getTeam()->getName();
        }
        $this->assertContains(needle: "ausrichterCD", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamBC", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamCD", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamDE", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamNL_1", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamNL_2", haystack: $teamnamenSetzliste);

        // Validiere Warteliste
        $warteliste = TurnierService::getWarteliste($turnier);
        $this->assertEquals(expected: "teamAB", actual: $warteliste->get($idAB)->getTeam()->getName());
        $this->assertEquals(expected: 1, actual: $warteliste->get($idAB)->getPositionWarteliste());
        $this->assertEquals(expected: "teamEF", actual: $warteliste->get($idEF)->getTeam()->getName());
        $this->assertEquals(expected: 2, actual: $warteliste->get($idEF)->getPositionWarteliste());
        $this->assertEquals(expected: "teamF", actual: $warteliste->get($idF)->getTeam()->getName());
        $this->assertEquals(expected: 3, actual: $warteliste->get($idF)->getPositionWarteliste());
    }

    public function testSetzlisteAuffuellenBlockfreiesTurnier(): void
    {
        // Turnier mit Anmeldungen erstellen
        $turnier = self::provideTurnier(block: "ABCDEF", phase: "setz", plaetze: 5);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "ausrichterCD", ligateam: true, block: "CD", liste: "setzliste", positionWarteliste: null);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamAB", ligateam: true, block: "AB", liste: "warteliste", positionWarteliste: 1);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamBC", ligateam: true, block: "BC", liste: "warteliste", positionWarteliste: 2);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamCD", ligateam: true, block: "CD", liste: "warteliste", positionWarteliste: 3);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamDE", ligateam: true, block: "DE", liste: "warteliste", positionWarteliste: 4);
        $idEF = $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamEF", ligateam: true, block: "EF", liste: "warteliste", positionWarteliste: 5);
        $idF = $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamF", ligateam: true, block: "F", liste: "warteliste", positionWarteliste: 6);
        $idNL_1 = $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamNL_1", ligateam: false, block: null, liste: "warteliste", positionWarteliste: 7);
        $idNL_2 = $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamNL_2", ligateam: false, block: null, liste: "warteliste", positionWarteliste: 8);

        // Testdurchführung
        TurnierService::setzListeAuffuellen($turnier, false);
        TurnierService::neueWartelistePositionen($turnier);

        // Validiere Setzliste
        $teamnamenSetzliste = [];
        $setzliste = TurnierService::getSetzListe($turnier);
        foreach ($setzliste as $anmeldung) {
            $teamnamenSetzliste[] = $anmeldung->getTeam()->getName();
        }
        $this->assertContains(needle: "ausrichterCD", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamAB", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamBC", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamCD", haystack: $teamnamenSetzliste);
        $this->assertContains(needle: "teamDE", haystack: $teamnamenSetzliste);

        // Validiere Warteliste
        $warteliste = TurnierService::getWarteliste($turnier);
        $this->assertEquals(expected: "teamEF", actual: $warteliste->get($idEF)->getTeam()->getName());
        $this->assertEquals(expected: 1, actual: $warteliste->get($idEF)->getPositionWarteliste());
        $this->assertEquals(expected: "teamF", actual: $warteliste->get($idF)->getTeam()->getName());
        $this->assertEquals(expected: 2, actual: $warteliste->get($idF)->getPositionWarteliste());
        $this->assertEquals(expected: "teamNL_1", actual: $warteliste->get($idNL_1)->getTeam()->getName());
        $this->assertEquals(expected: 3, actual: $warteliste->get($idNL_1)->getPositionWarteliste());
        $this->assertEquals(expected: "teamNL_2", actual: $warteliste->get($idNL_2)->getTeam()->getName());
        $this->assertEquals(expected: 4, actual: $warteliste->get($idNL_2)->getPositionWarteliste());
    }

    public function testSetzlisteAuffuellenInWartephase(): void
    {
        // Turnier mit Anmeldungen erstellen
        $turnier = self::provideTurnier(block: "CD", phase: "warte", plaetze: 5);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "ausrichterCD", ligateam: true, block: "CD", liste: "setzliste", positionWarteliste: null);
        $this->provideTeamAnmeldungMock(turnier: $turnier, name: "teamCD", ligateam: true, block: "CD", liste: "warteliste", positionWarteliste: 3);

        // Testdurchführung
        TurnierService::setzListeAuffuellen($turnier, false);
        TurnierService::neueWartelistePositionen($turnier);

        // Validiere Setzliste
        $teamnamenSetzliste = [];
        $setzliste = TurnierService::getSetzListe($turnier);
        $this->assertEquals(expected: "ausrichterCD", actual: $setzliste->first()->getTeam()->getName());
        $this->assertEquals(expected: 1, actual: $setzliste->count());

        // Validiere Warteliste
        $warteliste = TurnierService::getWarteliste($turnier);
        $this->assertEquals(expected: "teamCD", actual: $warteliste->first()->getTeam()->getName());
        $this->assertEquals(expected: 1, actual: $warteliste->count());
    }
}