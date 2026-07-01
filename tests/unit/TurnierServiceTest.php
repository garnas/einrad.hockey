<?php

namespace unit;

use App\Entity\Turnier\Turnier;
use App\Service\Turnier\TurnierService;
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
}
