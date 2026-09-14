<?php

namespace unit;

use App\Model\Spielplan\Turniertabellenzeile;
use App\Service\Spielplan\Turniertabellensortierung;
use App\Service\Spielplan\TurniertabellenService;
use PHPUnit\Framework\TestCase;

class TurniertabellenServiceTest extends TestCase
{
    /**
     * Torematrix aus einfachen Tupeln bauen: [spielId, teamA, teamB, toreA, toreB, penA, penB]
     *
     * @param array<int, array{0:int,1:int,2:int,3:?int,4:?int,5?:?int,6?:?int}> $spiele
     * @return array<int, array<int, list<array>>>
     */
    private function torematrix(array $spiele): array
    {
        $matrix = [];
        foreach ($spiele as $spiel) {
            [, $a, $b, $toreA, $toreB] = $spiel;
            $penA = $spiel[5] ?? null;
            $penB = $spiel[6] ?? null;
            $matrix[$a][$b][] = ['tore' => $toreA, 'gegentore' => $toreB, 'penalty_tore' => $penA, 'penalty_gegentore' => $penB];
            $matrix[$b][$a][] = ['tore' => $toreB, 'gegentore' => $toreA, 'penalty_tore' => $penB, 'penalty_gegentore' => $penA];
        }
        return $matrix;
    }

    public function testGetTurniertabelleAggregiertPunkteUndTore(): void
    {
        // T1 schlägt T2 5:1, T1 spielt 2:2 gegen T3
        $matrix = $this->torematrix([
            [1, 1, 2, 5, 1],
            [2, 1, 3, 2, 2],
        ]);

        $tabelle = TurniertabellenService::getTurniertabelle($matrix);

        $this->assertSame(4, $tabelle[1]->punkte); // 3 (Sieg) + 1 (Remis)
        $this->assertSame(2, $tabelle[1]->spiele);
        $this->assertSame(4, $tabelle[1]->tordifferenz); // (5-1) + (2-2)
        $this->assertSame(7, $tabelle[1]->tore);
        $this->assertSame(3, $tabelle[1]->gegentore);

        $this->assertSame(0, $tabelle[2]->punkte);
        $this->assertSame(1, $tabelle[3]->punkte);
    }

    public function testNichtGespielteBegegnungBleibtNull(): void
    {
        $matrix = $this->torematrix([[1, 1, 2, null, null]]);

        $tabelle = TurniertabellenService::getTurniertabelle($matrix);

        $this->assertSame(0, $tabelle[1]->spiele);
        $this->assertNull($tabelle[1]->punkte);
        $this->assertNull($tabelle[1]->tordifferenz);
    }

    public function testSortierungNachPunktenDannTordifferenzDannTore(): void
    {
        $zeilen = [
            10 => new Turniertabellenzeile(teamId: 10, spiele: 3, punkte: 6, tordifferenz: 2, tore: 8),
            20 => new Turniertabellenzeile(teamId: 20, spiele: 3, punkte: 9, tordifferenz: 1, tore: 4),
            30 => new Turniertabellenzeile(teamId: 30, spiele: 3, punkte: 6, tordifferenz: 5, tore: 9),
            40 => new Turniertabellenzeile(teamId: 40, spiele: 3, punkte: 6, tordifferenz: 2, tore: 12),
        ];

        $sortiert = array_keys((new Turniertabellensortierung())->sortiere($zeilen));

        // 20 (9 P) > 30 (6 P, +5) > 40 (6 P, +2, 12 Tore) > 10 (6 P, +2, 8 Tore)
        $this->assertSame([20, 30, 40, 10], $sortiert);
    }

    public function testSortierungPenaltyKriterienAlsTiebreak(): void
    {
        $zeilen = [
            1 => new Turniertabellenzeile(teamId: 1, punkte: 3, tordifferenz: 0, tore: 2, penaltyPunkte: 0),
            2 => new Turniertabellenzeile(teamId: 2, punkte: 3, tordifferenz: 0, tore: 2, penaltyPunkte: 3),
        ];

        $sortiert = array_keys((new Turniertabellensortierung())->sortiere($zeilen));

        $this->assertSame([2, 1], $sortiert);
    }
}
