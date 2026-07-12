<?php

namespace unit;

use App\Entity\Turnier\Turnier;
use App\Entity\TurnierBericht\TurnierBericht;
use PHPUnit\Framework\TestCase;

class TurnierBerichtTest extends TestCase
{
    public function testDefaults(): void
    {
        $turnier_stub = $this->createStub(Turnier::class);

        $bericht = new TurnierBericht($turnier_stub);
        $this->assertEquals(expected: 'Nein', actual: $bericht->getKaderUeberprueft());
        $this->assertEquals(expected: '', actual: $bericht->getBericht());
        $this->assertEquals(expected: $turnier_stub, actual: $bericht->getTurnier());
    }

    public function testBericht(): void
    {
        $turnier_stub = $this->createStub(Turnier::class);
        $empty = '';
        $text = 'ABCDEF';

        $bericht = new TurnierBericht($turnier_stub);
        $this->assertEquals(expected: $empty, actual: $bericht->getBericht());
        $bericht->setBericht($text);
        $this->assertEquals(expected: $text, actual: $bericht->getBericht());
        $bericht->setBericht($empty);
        $this->assertEquals(expected: $empty, actual: $bericht->getBericht());
    }

    public function testKaderUeberprueft(): void
    {
        $turnier_stub = $this->createStub(Turnier::class);

        $bericht = new TurnierBericht($turnier_stub);
        $this->assertEquals(expected: 'Nein', actual: $bericht->getKaderUeberprueft());
        $bericht->setKaderUeberprueft('Ja');
        $this->assertEquals(expected: 'Ja', actual: $bericht->getKaderUeberprueft());
        $bericht->setKaderUeberprueft('Nein');
        $this->assertEquals(expected: 'Nein', actual: $bericht->getKaderUeberprueft());
    }

    public function testTurnier(): void
    {
        $turnier_stub_a = $this->createStub(Turnier::class);
        $turnier_stub_b = $this->createStub(Turnier::class);

        $bericht = new TurnierBericht($turnier_stub_a);
        $this->assertEquals(expected: $turnier_stub_a, actual: $bericht->getTurnier());
        $bericht->setTurnier($turnier_stub_b);
        $this->assertEquals(expected: $turnier_stub_b, actual: $bericht->getTurnier());
        $bericht->setTurnier($turnier_stub_a);
        $this->assertEquals(expected: $turnier_stub_a, actual: $bericht->getTurnier());
    }
}
