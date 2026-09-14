<?php

namespace unit;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Spiel;
use App\Service\Spielplan\PlatzierungService;
use PHPUnit\Framework\TestCase;

class PlatzierungServiceTest extends TestCase
{
    private function team(int $id): nTeam
    {
        $team = new nTeam();
        (new \ReflectionProperty(nTeam::class, 'id'))->setValue($team, $id);
        return $team;
    }

    private function spiel(int $spielId, int $a, int $b, ?int $toreA, ?int $toreB, ?int $penA = null, ?int $penB = null): Spiel
    {
        return (new Spiel())
            ->setSpielId($spielId)
            ->setTeamA($this->team($a))
            ->setTeamB($this->team($b))
            ->setSchiriTeamA($this->team($a))
            ->setSchiriTeamB($this->team($b))
            ->setToreA($toreA)
            ->setToreB($toreB)
            ->setPenaltyA($penA)
            ->setPenaltyB($penB);
    }

    /**
     * @param int[] $ids
     * @return array<int, array{teamname: string, wertigkeit: null}>
     */
    private function teamliste(array $ids): array
    {
        $liste = [];
        foreach ($ids as $id) {
            $liste[$id] = ['teamname' => "T$id", 'wertigkeit' => null];
        }
        return $liste;
    }

    public function testEindeutigeHierarchie(): void
    {
        // T1 > T2 > T3 > T4, jeweils deutliche Siege
        $spiele = [
            1 => $this->spiel(1, 1, 2, 3, 0),
            2 => $this->spiel(2, 1, 3, 3, 0),
            3 => $this->spiel(3, 1, 4, 3, 0),
            4 => $this->spiel(4, 2, 3, 3, 0),
            5 => $this->spiel(5, 2, 4, 3, 0),
            6 => $this->spiel(6, 3, 4, 3, 0),
        ];

        $ergebnis = PlatzierungService::berechne($spiele, $this->teamliste([1, 2, 3, 4]));

        $this->assertSame([1, 2, 3, 4], array_keys($ergebnis->platzierungstabelle));
        $this->assertSame(1, $ergebnis->platzierungstabelle[1]->platz);
        $this->assertSame(4, $ergebnis->platzierungstabelle[4]->platz);
        $this->assertTrue($ergebnis->outOfScope === false);
        $this->assertEmpty($ergebnis->penaltys->gesamt);
    }

    public function testDirekterVergleichBeiPunktgleichheit(): void
    {
        // T1 gewinnt alles. T2/T3/T4 bilden einen Sieg-Zyklus (je 3 Punkte),
        // aufgelöst über die Tordifferenz im direkten Vergleich: T2 (+4) > T4 (-1) > T3 (-3).
        $spiele = [
            1 => $this->spiel(1, 1, 2, 4, 0),
            2 => $this->spiel(2, 1, 3, 4, 0),
            3 => $this->spiel(3, 1, 4, 4, 0),
            4 => $this->spiel(4, 2, 3, 5, 0),
            5 => $this->spiel(5, 3, 4, 2, 0),
            6 => $this->spiel(6, 4, 2, 1, 0),
        ];

        $ergebnis = PlatzierungService::berechne($spiele, $this->teamliste([1, 2, 3, 4]));

        $this->assertSame([1, 2, 4, 3], array_keys($ergebnis->platzierungstabelle));
        $this->assertSame(2, $ergebnis->platzierungstabelle[2]->platz);
        $this->assertSame(3, $ergebnis->platzierungstabelle[4]->platz);
        $this->assertSame(4, $ergebnis->platzierungstabelle[3]->platz);
        $this->assertCount(1, $ergebnis->direkterVergleichTabellen);
    }

    public function testPenaltyErmittlungBeiTordifferenzgleichheit(): void
    {
        // T1 gewinnt alles. T2/T3/T4 im Zyklus mit identischer Tordifferenz (je +0 im Zyklus)
        // -> Penaltybegegnungen zwischen T2, T3, T4.
        $spiele = [
            1 => $this->spiel(1, 1, 2, 5, 0),
            2 => $this->spiel(2, 1, 3, 5, 0),
            3 => $this->spiel(3, 1, 4, 5, 0),
            4 => $this->spiel(4, 2, 3, 1, 0),
            5 => $this->spiel(5, 3, 4, 1, 0),
            6 => $this->spiel(6, 4, 2, 1, 0),
        ];

        $ergebnis = PlatzierungService::berechne($spiele, $this->teamliste([1, 2, 3, 4]));

        $this->assertSame(1, $ergebnis->platzierungstabelle[1]->platz);
        // Die drei Zyklus-Begegnungen (Spiele 4, 5, 6) gehen ins Penaltyschießen
        sort($ergebnis->penaltys->gesamt);
        $this->assertSame([4, 5, 6], $ergebnis->penaltys->gesamt);
        // Noch nicht eingetragen -> alle ausstehend
        sort($ergebnis->penaltys->ausstehend);
        $this->assertSame([4, 5, 6], $ergebnis->penaltys->ausstehend);
    }
}
