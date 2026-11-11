<?php

namespace unit;

use App\Turnier\Entity\Turnier;
use App\Turnier\Service\TurnierFristenService;
use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TurnierFristenServiceTest extends TestCase
{
    private function turnier(string $datum, ?string $datumBis = null): Turnier
    {
        return (new Turnier())
            ->setDatum(new DateTime($datum))
            ->setDatumBis($datumBis === null ? null : new DateTime($datumBis));
    }

    private function format(DateTimeImmutable $datum): string
    {
        return $datum->format('D Y-m-d H:i:s');
    }

    // ------------------------------------------------------------------
    // Spieltag
    // ------------------------------------------------------------------

    public static function provideSpieltage(): array
    {
        // Spieltag Sa 02.05.2026 / Sa 09.05.2026
        return [
            "Samstag" => ["2026-05-02", "Sat 2026-05-02 00:00:00"],
            "Sonntag" => ["2026-05-03", "Sat 2026-05-02 00:00:00"],
            "Montag (vorheriger Spieltag)" => ["2026-05-04", "Sat 2026-05-02 00:00:00"],
            "Dienstag (vorheriger Spieltag)" => ["2026-05-05", "Sat 2026-05-02 00:00:00"],
            "Mittwoch (folgender Spieltag)" => ["2026-05-06", "Sat 2026-05-09 00:00:00"],
            "Donnerstag (folgender Spieltag)" => ["2026-05-14", "Sat 2026-05-16 00:00:00"],
            "Freitag (folgender Spieltag)" => ["2026-04-03", "Sat 2026-04-04 00:00:00"],
            "Uhrzeit wird ignoriert" => ["2026-05-03 18:30", "Sat 2026-05-02 00:00:00"],
            "über den Jahreswechsel" => ["2026-12-29", "Sat 2026-12-26 00:00:00"],
        ];
    }

    #[DataProvider("provideSpieltage")]
    public function testSpieltagBeginn(string $datum, string $erwartet): void
    {
        $this->assertSame($erwartet, $this->format(TurnierFristenService::spieltagBeginn($this->turnier($datum))));
    }

    // ------------------------------------------------------------------
    // Fristen vor dem Turnier (Beispiel aus der Klassendokumentation)
    // ------------------------------------------------------------------

    public static function provideTurniertage(): array
    {
        // Alle Turniere gehören zum Spieltag Sa 02.05.2026
        return [
            "Samstag" => ["2026-05-02"],
            "Sonntag" => ["2026-05-03"],
            "Montag" => ["2026-05-04"],
            "Dienstag" => ["2026-05-05"],
        ];
    }

    #[DataProvider("provideTurniertage")]
    public function testFreilosFrist(string $datum): void
    {
        $this->assertSame("Sat 2026-03-07 00:00:00", $this->format(TurnierFristenService::freilosFrist($this->turnier($datum))));
    }

    #[DataProvider("provideTurniertage")]
    public function testSetzphaseAb(string $datum): void
    {
        $this->assertSame("Sat 2026-04-04 00:00:00", $this->format(TurnierFristenService::setzphaseAb($this->turnier($datum))));
    }

    #[DataProvider("provideTurniertage")]
    public function testAbmeldeFrist(string $datum): void
    {
        $this->assertSame("Fri 2026-04-17 23:59:59", $this->format(TurnierFristenService::abmeldeFrist($this->turnier($datum))));
    }

    #[DataProvider("provideTurniertage")]
    public function testSpielplanphaseAb(string $datum): void
    {
        $this->assertSame("Tue 2026-04-21 23:59:00", $this->format(TurnierFristenService::spielplanphaseAb($this->turnier($datum))));
    }

    public function testAbmeldeFristUeberDieZeitumstellung(): void
    {
        // Umstellung auf Sommerzeit am 29.03.2026: Frist bleibt Freitag 23:59 Ortszeit
        $this->assertSame(
            "Fri 2026-03-27 23:59:59",
            $this->format(TurnierFristenService::abmeldeFrist($this->turnier("2026-04-11"))),
        );
    }

    // ------------------------------------------------------------------
    // Ergebnis- und Berichtsfrist
    // ------------------------------------------------------------------

    public static function provideErgebnisFristen(): array
    {
        return [
            "Samstag" => ["2026-05-02", null, "Tue 2026-05-05 23:59:59"],
            "Sonntag" => ["2026-05-03", null, "Tue 2026-05-05 23:59:59"],
            "Montag" => ["2026-05-04", null, "Tue 2026-05-05 23:59:59"],
            "Dienstag: am selben Tag" => ["2026-05-05", null, "Tue 2026-05-05 23:59:59"],
            "Mittwoch" => ["2026-05-06", null, "Tue 2026-05-12 23:59:59"],
            "mehrtägig: ab dem letzten Tag" => ["2026-05-02", "2026-05-03", "Tue 2026-05-05 23:59:59"],
            "mehrtägig bis Dienstag" => ["2026-05-02", "2026-05-05", "Tue 2026-05-05 23:59:59"],
            "mehrtägig bis Mittwoch" => ["2026-05-02", "2026-05-06", "Tue 2026-05-12 23:59:59"],
            // bisherige Testfälle der Turnierreport-Frist
            ["2026-07-13", null, "Tue 2026-07-14 23:59:59"],
            ["2026-07-21", null, "Tue 2026-07-21 23:59:59"],
            ["2026-07-29", null, "Tue 2026-08-04 23:59:59"],
            ["2026-08-06", null, "Tue 2026-08-11 23:59:59"],
            ["2026-08-14", null, "Tue 2026-08-18 23:59:59"],
            ["2026-08-22", null, "Tue 2026-08-25 23:59:59"],
            ["2026-08-30", null, "Tue 2026-09-01 23:59:59"],
        ];
    }

    #[DataProvider("provideErgebnisFristen")]
    public function testErgebnisFrist(string $datum, ?string $datumBis, string $erwartet): void
    {
        $this->assertSame($erwartet, $this->format(TurnierFristenService::ergebnisFrist($this->turnier($datum, $datumBis))));
    }

    #[DataProvider("provideErgebnisFristen")]
    public function testBerichtFristIstErgebnisFrist(string $datum, ?string $datumBis, string $erwartet): void
    {
        $this->assertSame($erwartet, $this->format(TurnierFristenService::berichtFrist($this->turnier($datum, $datumBis))));
    }

    public static function provideOffen(): array
    {
        // bisherige Testfälle der Turnierreport-Frist
        return [
            ['2026-07-13', '2026-07-13 15:48:33', true],
            ['2026-07-21', '2026-07-21 23:29:13', true],
            ['2026-07-29', '2026-08-04 23:36:51', true],
            ['2026-08-14', '2026-08-18 20:03:48', true],
            ['2026-08-30', '2026-09-01 17:33:36', true],
            ['2026-07-21', '2026-07-21 23:59:59', true],
            ['2026-07-21', '2026-07-22 00:00:00', false],
            ['2026-07-13', '2026-07-17 21:26:42', false],
            ['2026-07-29', '2026-08-07 00:09:51', false],
            ['2026-08-22', '2026-08-28 22:23:56', false],
        ];
    }

    #[DataProvider("provideOffen")]
    public function testIsOffen(string $datum, string $jetzt, bool $offen): void
    {
        $this->assertSame(
            $offen,
            TurnierFristenService::isOffen(
                TurnierFristenService::berichtFrist($this->turnier($datum)),
                new DateTimeImmutable($jetzt),
            ),
        );
    }
}
