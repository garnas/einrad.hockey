<?php

namespace unit;

use App\Turnier\Entity\Turnier;
use App\Turnier\Service\TurnierService;
use DateTime;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TurnierServiceTest extends TestCase
{
    public function testBlockFrei(): void
    {
        $turnier = new Turnier();

        $turnier->setBlock("A");
        TurnierService::erweitereBlockFrei($turnier);
        $this->assertEquals(expected: "ABCDEF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertFrei());

        $turnier->setBlock("AB");
        TurnierService::erweitereBlockFrei($turnier);
        $this->assertEquals(expected: "ABCDEF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertFrei());

        $turnier->setBlock("BC");
        TurnierService::erweitereBlockFrei($turnier);
        $this->assertEquals(expected: "ABCDEF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertFrei());

        $turnier->setBlock("CD");
        TurnierService::erweitereBlockFrei($turnier);
        $this->assertEquals(expected: "ABCDEF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertFrei());

        $turnier->setBlock("DE");
        TurnierService::erweitereBlockFrei($turnier);
        $this->assertEquals(expected: "ABCDEF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertFrei());

        $turnier->setBlock("EF");
        TurnierService::erweitereBlockFrei($turnier);
        $this->assertEquals(expected: "ABCDEF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertFrei());

        $turnier->setBlock("F");
        TurnierService::erweitereBlockFrei($turnier);
        $this->assertEquals(expected: "ABCDEF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertFrei());

    }

    public function testBlockErweiternHoch(): void
    {
        $turnier = new Turnier();

        $turnier->setBlock("A");
        TurnierService::erweitereBlockHoch($turnier);
        $this->assertEquals(expected: "A", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertHoch());

        $turnier->setBlock("AB");
        TurnierService::erweitereBlockHoch($turnier);
        $this->assertEquals(expected: "AB", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertHoch());

        $turnier->setBlock("BC");
        TurnierService::erweitereBlockHoch($turnier);
        $this->assertEquals(expected: "ABC", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertHoch());

        $turnier->setBlock("CD");
        TurnierService::erweitereBlockHoch($turnier);
        $this->assertEquals(expected: "BCD", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertHoch());

        $turnier->setBlock("DE");
        TurnierService::erweitereBlockHoch($turnier);
        $this->assertEquals(expected: "CDE", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertHoch());

        $turnier->setBlock("EF");
        TurnierService::erweitereBlockHoch($turnier);
        $this->assertEquals(expected: "DEF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertHoch());

        $turnier->setBlock("F");
        TurnierService::erweitereBlockHoch($turnier);
        $this->assertEquals(expected: "EF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertHoch());
    }


    public function testBlockErweiternRunter(): void
    {
        $turnier = new Turnier();

        $turnier->setBlock("A");
        TurnierService::erweitereBlockRunter($turnier);
        $this->assertEquals(expected: "AB", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertRunter());

        $turnier->setBlock("AB");
        TurnierService::erweitereBlockRunter($turnier);
        $this->assertEquals(expected: "ABC", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertRunter());

        $turnier->setBlock("BC");
        TurnierService::erweitereBlockRunter($turnier);
        $this->assertEquals(expected: "BCD", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertRunter());

        $turnier->setBlock("CD");
        TurnierService::erweitereBlockRunter($turnier);
        $this->assertEquals(expected: "CDE", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertRunter());

        $turnier->setBlock("DE");
        TurnierService::erweitereBlockRunter($turnier);
        $this->assertEquals(expected: "DEF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertRunter());

        $turnier->setBlock("EF");
        TurnierService::erweitereBlockRunter($turnier);
        $this->assertEquals(expected: "EF", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertRunter());

        $turnier->setBlock("F");
        TurnierService::erweitereBlockRunter($turnier);
        $this->assertEquals(expected: "F", actual: $turnier->getBlock());
        $this->assertEquals(expected: true, actual: $turnier->isBlockErweitertRunter());
    }

    public static function provideSpassturnierBeendetAb(): array
    {
        return [
            "Samstag ohne Enddatum" => ["2026-11-07", null, "2026-11-10"],
            "Sonntag ohne Enddatum" => ["2026-11-08", null, "2026-11-10"],
            "Montag ohne Enddatum" => ["2026-11-09", null, "2026-11-10"],
            "Dienstag ohne Enddatum" => ["2026-11-10", null, "2026-11-17"],
            "Mittwoch ohne Enddatum" => ["2026-11-11", null, "2026-11-17"],
            "Wochenende mit Enddatum" => ["2026-05-02", "2026-05-03", "2026-05-04"],
            "Enddatum am Montag" => ["2026-05-02", "2026-05-04", "2026-05-05"],
            "Enddatum am Monatsende" => ["2026-10-30", "2026-10-31", "2026-11-01"],
        ];
    }

    #[DataProvider("provideSpassturnierBeendetAb")]
    public function testSpassturnierBeendetAb(string $datum, ?string $datumBis, string $erwartet): void
    {
        $turnier = (new Turnier())
            ->setDatum(new DateTime($datum))
            ->setDatumBis($datumBis === null ? null : new DateTime($datumBis));

        $this->assertEquals(
            expected: $erwartet,
            actual: TurnierService::spassturnierBeendetAb($turnier)->format("Y-m-d"),
        );
    }

    public function testSpassturnierBeendetAbIgnoriertUhrzeit(): void
    {
        $turnier = (new Turnier())
            ->setDatum(new DateTime("2026-11-07 18:30:00"))
            ->setDatumBis(null);

        $this->assertEquals(
            expected: "2026-11-10 00:00:00",
            actual: TurnierService::spassturnierBeendetAb($turnier)->format("Y-m-d H:i:s"),
        );
    }
}
