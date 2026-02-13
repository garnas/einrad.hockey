<?php

namespace integration;

use db;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Stats;


class StatsTest extends TestCase
{
    protected function deleteSpielerstatistik(): void
    {
        $sql = "
            DELETE FROM spieler_statistik WHERE 1;
        ";
        db::$db->query($sql)->log();
    }
    protected function provideDataForSpieleranzahl()
    {
        self::deleteSpielerstatistik();
        $sql = "
            INSERT INTO db_localhost.spieler_statistik (id, date, saison, geschlecht, anzahl)
            VALUES
                (2, '2025-06-30 13:03:30', 31, 'm', 123),
                (3, '2026-01-29 23:07:59', 31, NULL, 3),
                (4, '2026-01-29 23:07:59', 31, 'm', 318),
                (5, '2026-01-29 23:07:59', 31, 'w', 304),
                (6, '2026-01-29 23:07:59', 31, 'd', 1),
                (7, '2026-02-24 00:07:59', 31, 'w', 1000);
        ";
        db::$db->query($sql)->log();
    }

    public static function provideCutoffs(): array
    {

        return [
            ["now" => "9999-12-31 00:00:00", "cutoff" => "30.06.9999", "anzahl" => 1000],
            ["now" => "2026-06-31 00:00:00", "cutoff" => "30.06.2026", "anzahl" => 1000],
            ["now" => "2026-06-30 23:59:58", "cutoff" => "31.01.2026", "anzahl" => 626],
            ["now" => "2026-02-01 00:00:00", "cutoff" => "31.01.2026", "anzahl" => 626],
            ["now" => "2026-01-31 23:59:58", "cutoff" => "30.06.2025", "anzahl" => 123],
            ["now" => "2025-07-01 00:00:00", "cutoff" => "30.06.2025", "anzahl" => 123],
            ["now" => "2025-06-30 23:59:58", "cutoff" => "31.01.2025", "anzahl" => 0],
        ];
    }

    #[DataProvider("provideCutoffs")]
    public function testSpieleranzahlCutoffDates(string $now, string $cutoff, int $anzahl): void
    {
        self::provideDataForSpieleranzahl();

        $time = strtotime($now);
        $spieleranzahl = Stats::get_aktuelle_spieler_anzahl($time);
        $this->assertEquals(
            expected: $cutoff,
            actual: $spieleranzahl["cutoff"],
        );
        $this->assertEquals(
            expected: $anzahl,
            actual: $spieleranzahl["number"],
        );
    }

    public function testPersistSpielerstatistik(): void
    {
        self::deleteSpielerstatistik();
        Stats::persist_spieler_statistik();
        $spieleranzahl = Stats::get_aktuelle_spieler_anzahl();
        $this->assertTrue(
            condition: is_string($spieleranzahl["cutoff"]),
        );
        $this->assertEquals(
            expected: 0,
            actual: $spieleranzahl["number"],
        );
        # Sechs Monate in die Zukunft
        $spieleranzahl = Stats::get_aktuelle_spieler_anzahl(time() + 6*30*24*3600);
        $this->assertTrue(
            condition: is_string($spieleranzahl["cutoff"]),
        );
        $this->assertTrue(
            condition: $spieleranzahl["number"] >= 1,
        );
    }


}