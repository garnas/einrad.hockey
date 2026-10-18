<?php

namespace unit;

use App\Team\Entity\Team;
use App\Spieler\Entity\Spieler;
use App\Team\Service\TeamService;
use Config;
use Doctrine\Common\Collections\ArrayCollection;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class TeamServiceTest extends TestCase
{
    private function teamMitKader(int $aktuell, int $vorsaison = 0): Team
    {
        $kader = new ArrayCollection();
        for ($i = 0; $i < $aktuell; $i++) {
            $kader->add((new Spieler())->setLetzteSaison(Config::SAISON));
        }
        for ($i = 0; $i < $vorsaison; $i++) {
            $kader->add((new Spieler())->setLetzteSaison(Config::SAISON - 1));
        }
        return (new Team())->setKader($kader);
    }

    public static function provideKadergroessen(): array
    {
        return [
            "leerer Kader" => [0, 0, false],
            "vier Spieler" => [4, 0, false],
            "fünf Spieler" => [5, 0, true],
            "sechs Spieler" => [6, 0, true],
            "Vorsaison zählt nicht" => [3, 5, false],
            "aktuell und Vorsaison" => [5, 3, true],
        ];
    }

    #[DataProvider("provideKadergroessen")]
    public function testCheckKaderForTurniererstellung(int $aktuell, int $vorsaison, bool $erwartet): void
    {
        $this->assertSame(
            expected: $erwartet,
            actual: TeamService::checkKaderForTurniererstellung($this->teamMitKader($aktuell, $vorsaison)),
        );
    }
}
