<?php

namespace unit;

use App\Entity\Team\nTeam;
use App\Entity\Turnier\Turnier;
use App\Service\Turnier\BlockService;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;


class BlockServiceTest extends TestCase
{
    public static function provideHoehereTurnierBlocks(): array
    {
        return [
            ["CDE", "BCDE"],
            ["CD", "BCD"],
            ["EF", "DEF"],
            ["F", "EF"],
        ];
    }

    #[DataProvider("provideHoehereTurnierBlocks")]
    public function testHoehererTurnierBlock(string $block, string $hoehererBlock): void
    {
        $turnier = new Turnier();
        $turnier->setBlock($block);
        $this->assertEquals(
            expected: $hoehererBlock,
            actual: BlockService::hoehererTurnierBlock($turnier)
        );
    }

    public static function provideNiedrigereTurnierBlocks(): array
    {
        return [
            ["CDE", "CDEF"],
            ["CD", "CDE"],
            ["AB", "ABC"],
            ["A", "AB"],
        ];
    }

    #[DataProvider("provideNiedrigereTurnierBlocks")]
    public function testNiedrigererTurnierBlock(string $block, string $hoehererBlock): void
    {
        $turnier = new Turnier();
        $turnier->setBlock($block);
        $this->assertEquals(
            expected: $hoehererBlock,
            actual: BlockService::niedrigererTurnierBlock($turnier)
        );
    }

    public static function provideIsBlockPassend(): array
    {
        return [
            ["turnierblock" => "CDE", "teamblock" => "CD", "passend" => true],
            ["turnierblock" => "A", "teamblock" => "CD", "passend" => false],
            ["turnierblock" => "A", "teamblock" => "F", "passend" => false],
            ["turnierblock" => "A", "teamblock" => "A", "passend" => true],
            ["turnierblock" => "BCDE", "teamblock" => "CD", "passend" => true],
            ["turnierblock" => "BCDE", "teamblock" => "EF", "passend" => true],
            ["turnierblock" => "BCDE", "teamblock" => "AB", "passend" => true],
            ["turnierblock" => "BCDE", "teamblock" => "A", "passend" => false],
            ["turnierblock" => "BCDE", "teamblock" => "F", "passend" => false],
            ["turnierblock" => "DEF", "teamblock" => "F", "passend" => true],
            ["turnierblock" => "DEF", "teamblock" => "EF", "passend" => true],
            ["turnierblock" => "DEF", "teamblock" => "BC", "passend" => false],
            ["turnierblock" => "ABCDEF", "teamblock" => "F", "passend" => true],
            ["turnierblock" => "ABCDEF", "teamblock" => "CD", "passend" => true],

        ];
    }

    #[DataProvider("provideIsBlockPassend")]
    public function testIsBlockPassend(string $turnierblock, string $teamblock, bool $passend)
    {
        $turnier = (new Turnier())->setBlock($turnierblock);
        $teamMock = $this->createMock(nTeam::class);
        $teamMock->method('getBlock')->willReturn($teamblock);
        $this->assertEquals(
            expected: $passend,
            actual: BlockService::isBlockPassend($turnier, $teamMock)
        );
    }

    public static function provideIsTurnierblockHoeher(): array
    {
        return [
            ["turnierblock" => "CDE", "teamblock" => "CD", "hoeher" => false],
            ["turnierblock" => "A", "teamblock" => "CD", "hoeher" => true],
            ["turnierblock" => "A", "teamblock" => "F", "hoeher" => true],
            ["turnierblock" => "A", "teamblock" => "A", "hoeher" => false],
            ["turnierblock" => "ABC", "teamblock" => "DE", "hoeher" => true],
            ["turnierblock" => "BCDE", "teamblock" => "F", "hoeher" => true],
            ["turnierblock" => "BCDE", "teamblock" => "A", "hoeher" => false],
            ["turnierblock" => "CDE", "teamblock" => "F", "hoeher" => true],
            ["turnierblock" => "DE", "teamblock" => "F", "hoeher" => true],
            ["turnierblock" => "F", "teamblock" => "F", "hoeher" => false],
            ["turnierblock" => "F", "teamblock" => "A", "hoeher" => false],

        ];
    }

    #[DataProvider("provideIsTurnierblockHoeher")]
    public function testIsTurnierBlockHigher(string $turnierblock, string $teamblock, bool $hoeher)
    {
        $turnier = (new Turnier())->setBlock($turnierblock);
        $teamMock = $this
            ->createMock(nTeam::class);
        $teamMock->method('getBlock')->willReturn($teamblock);
        $this->assertEquals(
            expected: $hoeher,
            actual: BlockService::isTurnierBlockHigher($turnier, $teamMock)
        );
    }
}