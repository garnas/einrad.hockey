<?php

namespace unit;

use App\Turnier\Service\TurnierPermission;
use Helper;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TurnierPermissionTest extends TestCase
{
    private bool $ligacenterVorher;

    protected function setUp(): void
    {
        $this->ligacenterVorher = Helper::$ligacenter;
    }

    protected function tearDown(): void
    {
        Helper::$ligacenter = $this->ligacenterVorher;
    }

    public static function provideArten(): array
    {
        return [
            "I" => ["I", true, true],
            "II" => ["II", true, true],
            "III" => ["III", false, false],
            "final" => ["final", false, true],
            "fixed" => ["fixed", false, true],
            "spass" => ["spass", true, true],
            "unbekannt" => ["IV", false, false],
            "ohne Art" => [null, false, false],
        ];
    }

    #[DataProvider("provideArten")]
    public function testTeamDarfArtAnlegen(?string $art, bool $team, bool $la): void
    {
        Helper::$ligacenter = false;

        $this->assertSame(expected: $team, actual: TurnierPermission::canCreateArt($art));
    }

    #[DataProvider("provideArten")]
    public function testLaDarfArtAnlegen(?string $art, bool $team, bool $la): void
    {
        Helper::$ligacenter = true;

        $this->assertSame(expected: $la, actual: TurnierPermission::canCreateArt($art));
    }
}
