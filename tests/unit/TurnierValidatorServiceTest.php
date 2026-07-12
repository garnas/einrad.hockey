<?php

namespace unit;

use App\Entity\Turnier\Turnier;
use App\Service\Turnier\TurnierValidatorService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TurnierValidatorServiceTest extends TestCase
{
    public static function provideIsErweiterbarBlockrunter(): array
    {
        return [
            [
                "block" => "A",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "AB",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "BC",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "CD",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "EF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "F",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "AB",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "ABC",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "BCD",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "CDE",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DEF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "ABC",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => true,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "BCD",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => true,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "CDE",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => true,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DEF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => true,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "EF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => true,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "ABCDEF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => true,
                "isErweiterbar" => false,
            ],
            [
                "block" => "A",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "AB",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "BC",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "CD",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "EF",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "F",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
        ];
    }

    #[DataProvider("provideIsErweiterbarBlockrunter")]
    public function testIsErweiterbarBlockrunter($block, $art, $phase, $isErweitertBlockrunter, $isErweitertBlockhoch, $isErweitertBlockfrei, $isErweiterbar): void
    {
        $turnier = (new Turnier())
            ->setBlock($block)
            ->setArt($art)
            ->setPhase($phase)
            ->setBlockErweitertRunter($isErweitertBlockrunter)
            ->setBlockErweitertHoch($isErweitertBlockhoch)
            ->setBlockErweitertFrei($isErweitertBlockfrei);

        $this->assertEquals(
            expected: $isErweiterbar,
            actual: TurnierValidatorService::isErweiterbarBlockrunter($turnier),
        );
    }

    public static function provideIsErweiterbarBlockhoch(): array
    {
        return [
            [
                "block" => "A",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "AB",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "BC",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "CD",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "EF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "F",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "ABC",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => true,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "BCD",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => true,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "CDE",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => true,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DEF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => true,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "ABC",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "BCD",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "CDE",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DEF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "EF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "ABCDEF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => true,
                "isErweiterbar" => false,
            ],
            [
                "block" => "A",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "AB",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "BC",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "CD",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "EF",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "F",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockrunter" => false,
                "isErweitertBlockhoch" => false,
                "isErweitertBlockfrei" => false,
                "isErweiterbar" => false,
            ],
        ];
    }

    #[DataProvider("provideIsErweiterbarBlockhoch")]
    public function testIsErweiterbarBlockhoch($block, $art, $phase, $isErweitertBlockhoch, $isErweitertBlockrunter, $isErweitertBlockfrei, $isErweiterbar): void
    {
        $turnier = (new Turnier())
            ->setBlock($block)
            ->setArt($art)
            ->setPhase($phase)
            ->setBlockErweitertHoch($isErweitertBlockhoch)
            ->setBlockErweitertRunter($isErweitertBlockrunter)
            ->setBlockErweitertFrei($isErweitertBlockfrei);

        $this->assertEquals(
            expected: $isErweiterbar,
            actual: TurnierValidatorService::isErweiterbarBlockhoch($turnier),
        );
    }

    public static function provideIsErweiterbarBlockfrei(): array
    {
        return [
            [
                "block" => "ABCDEF",
                "art" => "I",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DE",
                "art" => "I",
                "phase" => "spielplan",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "spielplan",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "ergebnis",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "F",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "A",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "AB",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => true,
                "isErweiterbar" => false,
            ],
            [
                "block" => "EF",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => true,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "CD",
                "art" => "final",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "ABCDEF",
                "art" => "I",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "BC",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => true,
            ],
            [
                "block" => "BCD",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => true,
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false,
            ],
            [
                "block" => "BCD",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweitertBlockrunter" => true,
                "isErweiterbar" => false,
            ],
        ];
    }

    #[DataProvider("provideIsErweiterbarBlockfrei")]
    public function testIsErweiterbarBlockfrei($block, $art, $phase, $isErweitertBlockhoch, $isErweitertBlockrunter, $isErweiterbar): void
    {
        $turnier = (new Turnier())
            ->setBlock($block)
            ->setArt($art)
            ->setPhase($phase)
            ->setBlockErweitertFrei(false)
            ->setBlockErweitertHoch($isErweitertBlockhoch)
            ->setBlockErweitertRunter($isErweitertBlockrunter);

        $this->assertEquals(
            expected: $isErweiterbar,
            actual: TurnierValidatorService::isErweiterbarBlockfrei($turnier),
        );
    }

}
