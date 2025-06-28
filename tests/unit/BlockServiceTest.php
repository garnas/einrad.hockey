<?php

namespace unit;

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

}