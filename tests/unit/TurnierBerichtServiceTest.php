<?php

namespace unit;

use DateTime;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;
use App\Entity\Turnier\Turnier;
use App\Service\TurnierBericht\TurnierBerichtService;

class TurnierBerichtServiceTest extends TestCase
{
    public static function provideDates(): array
    {
        return [
            ["2026-07-13", "2026-07-14 23:59:59"],
            ["2026-07-21", "2026-07-21 23:59:59"],
            ["2026-07-29", "2026-08-04 23:59:59"],
            ["2026-08-06", "2026-08-11 23:59:59"],
            ["2026-08-14", "2026-08-18 23:59:59"],
            ["2026-08-22", "2026-08-25 23:59:59"],
            ["2026-08-30", "2026-09-01 23:59:59"],
        ];
    }

    #[DataProvider("provideDates")]
    public function testCalcBearbeitungFrist(string $turnierdatum, string $fristdatum)
    {
        $turnierdatum = DateTime::createFromFormat('Y-m-d', $turnierdatum);
        $fristdatum = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $fristdatum);

        $turnier = $this->createStub(Turnier::class);
        $turnier->method('getDatum')->willReturn($turnierdatum);
        $this->assertEquals(
            expected: $fristdatum,
            actual: TurnierBerichtService::getBearbeitungFrist($turnier)
        );
    }

    public static function providBoolDates(): array
    {
        return [
            ['2026-07-13', '2026-07-13 15:48:33', true],
            ['2026-07-21', '2026-07-21 09:58:52', true],
            ['2026-07-29', '2026-08-04 23:36:51', true],
            ['2026-08-06', '2026-08-07 00:34:31', true],
            ['2026-08-14', '2026-08-17 01:52:19', true],
            ['2026-08-22', '2026-08-23 00:29:35', true],
            ['2026-08-30', '2026-08-31 17:34:57', true],
            ['2026-07-13', '2026-07-14 06:49:12', true],
            ['2026-07-21', '2026-07-21 23:29:13', true],
            ['2026-07-29', '2026-08-04 19:46:50', true],
            ['2026-08-06', '2026-08-11 20:58:56', true],
            ['2026-08-14', '2026-08-18 20:03:48', true],
            ['2026-08-22', '2026-08-25 04:36:46', true],
            ['2026-08-30', '2026-09-01 17:33:36', true],
            ['2026-07-13', '2026-07-17 21:26:42', false],
            ['2026-07-21', '2026-07-24 09:44:19', false],
            ['2026-07-29', '2026-08-07 00:09:51', false],
            ['2026-08-06', '2026-08-13 20:50:27', false],
            ['2026-08-14', '2026-08-21 05:33:45', false],
            ['2026-08-22', '2026-08-28 22:23:56', false],
            ['2026-08-30', '2026-09-04 02:49:21', false],
        ];

    }
    #[DataProvider("providBoolDates")]
    public function testInBearbeitungFrist(string $turnierdatum, string $bearbeitungdatum, bool $value)
    {
        $turnierdatum = DateTime::createFromFormat('Y-m-d', $turnierdatum);
        $bearbeitungdatum = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', $bearbeitungdatum);

        $turnier = $this->createStub(Turnier::class);
        $turnier->method('getDatum')->willReturn($turnierdatum);
        $this->assertEquals(
            expected: $value,
            actual: TurnierBerichtService::isInBearbeitungFrist($turnier, $bearbeitungdatum)
        );
    }
}
