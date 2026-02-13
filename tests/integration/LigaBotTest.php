<?php

namespace integration;

use App\Event\Turnier\nLigaBot;
use PHPUnit\Framework\TestCase;


class LigaBotTest extends TestCase
{
    /**
     * Der Ligabot soll ohne Fehler durchlaufen.
     */
    public function testLigabot(): void
    {
        $this->expectNotToPerformAssertions();
        nLigaBot::ligaBot();

    }
}