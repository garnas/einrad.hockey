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
                "block" => "CDE",
                "art" => "I",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => true
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => true
            ],
            [
                "block" => "F",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false
            ],
            [
                "block" => "A",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => null,
                "isErweiterbar" => true
            ],
            [
                "block" => "CD",
                "art" => "final",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false
            ],
            [
                "block" => "ABCDEF",
                "art" => "I",
                "phase" => "setz",
                "isErweitertBlockrunter" => false,
                "isErweiterbar" => false
            ],
            [
                "block" => "A",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockrunter" => true,
                "isErweiterbar" => false
            ],
        ];
    }

    #[DataProvider("provideIsErweiterbarBlockrunter")]
    public function testIsErweiterbarBlockrunter($block, $art, $phase, $isErweitertBlockrunter, $isErweiterbar): void
    {
        $turnier = (new Turnier())
            ->setBlock($block)
            ->setArt($art)
            ->setPhase($phase)
            ->setBlockErweitertRunter($isErweitertBlockrunter);

        $this->assertEquals(
            expected: $isErweiterbar,
            actual: TurnierValidatorService::isErweiterbarBlockrunter($turnier),
        );
    }

    public static function provideIsErweiterbarBlockhoch(): array
    {
        return [
            [
                "block" => "CDE",
                "art" => "I",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweiterbar" => true
            ],
            [
                "block" => "CDE",
                "art" => "I",
                "phase" => "spielplan",
                "isErweitertBlockhoch" => false,
                "isErweiterbar" => false
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweiterbar" => true
            ],
            [
                "block" => "DE",
                "art" => "II",
                "phase" => "warte",
                "isErweitertBlockhoch" => false,
                "isErweiterbar" => false
            ],
            [
                "block" => "F",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweiterbar" => true
            ],
            [
                "block" => "A",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweiterbar" => false
            ],
            [
                "block" => "CD",
                "art" => "final",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweiterbar" => false
            ],
            [
                "block" => "ABCDEF",
                "art" => "I",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweiterbar" => false
            ],
            [
                "block" => "BC",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => null,
                "isErweiterbar" => true
            ],
            [
                "block" => "BC",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => false,
                "isErweiterbar" => true
            ],
            [
                "block" => "BCD",
                "art" => "II",
                "phase" => "setz",
                "isErweitertBlockhoch" => true,
                "isErweiterbar" => false
            ],
        ];
    }

    #[DataProvider("provideIsErweiterbarBlockhoch")]
    public function testIsErweiterbarBlockhoch($block, $art, $phase, $isErweitertBlockhoch, $isErweiterbar): void
    {
        $turnier = (new Turnier())
            ->setBlock($block)
            ->setArt($art)
            ->setPhase($phase)
            ->setBlockErweitertHoch($isErweitertBlockhoch);

        $this->assertEquals(
            expected: $isErweiterbar,
            actual: TurnierValidatorService::isErweiterbarBlockhoch($turnier),
        );
    }
}