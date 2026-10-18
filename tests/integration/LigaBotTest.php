<?php

namespace integration;

use App\Turnier\Bot\LigaBot;
use PHPUnit\Framework\TestCase;

class LigaBotTest extends TestCase
{
    /**
     * Der Ligabot soll ohne Fehler durchlaufen.
     */
    public function testLigabot(): void
    {
        $this->expectNotToPerformAssertions();
        LigaBot::ligaBot();
    }
}
