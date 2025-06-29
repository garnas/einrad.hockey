<?php

namespace unit;

use App\Entity\Turnier\Turnier;
use App\Service\Turnier\TurnierService;
use PHPUnit\Framework\TestCase;

class TurnierServiceTest extends TestCase
{
    public function testBlockOeffnen(): void
    {
        $turnier = new Turnier();
        $turnier->setBlock("AB");

        TurnierService::blockOeffnen($turnier);

        $this->assertEquals(
            expected: "ABCDEF",
            actual: $turnier->getBlock()
        );
    }

    public function testBlockErweiternRunter(): void
    {
        $turnier = new Turnier();
        $turnier->setBlock("AB");

        TurnierService::erweitereBlockRunter($turnier);

        $this->assertEquals(
            expected: "ABC",
            actual: $turnier->getBlock()
        );
    }

    public function testBlockErweiternHoch(): void
    {
        $turnier = new Turnier();
        $turnier->setBlock("BCD");

        TurnierService::erweitereBlockHoch($turnier);

        $this->assertEquals(
            expected: "ABCD",
            actual: $turnier->getBlock()
        );
    }
}