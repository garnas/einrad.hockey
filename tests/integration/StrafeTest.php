<?php

namespace integration;

use App\Entity\Team\Team;
use App\Entity\Team\Strafe;
use App\Entity\Team\Teamdetails;
use App\Repository\Team\TeamRepository;
use App\Repository\Turnier\TurnierRepository;
use Config;
use PHPUnit\Framework\TestCase;

class StrafeTest extends TestCase
{
    protected function provideTeam(): Team
    {
        $team = TeamRepository::get()->findByName("ABC Testteam");
        if ($team) {
            TeamRepository::get()->delete($team);
        }
        $team = (new Team())
            ->setName("ABC Testteam")
            ->setLigateam("Ja")
            ->setAktiv("Ja");
        $team->setDetails(
            (new Teamdetails())
                ->setTeam($team),
        );
        TeamRepository::get()->speichern($team);
        return TeamRepository::get()->team($team->id());
    }

    public function testVerwarnungOhneTurnier(): void
    {
        $team = self::provideTeam();
        $this->assertEquals(expected: 0, actual: $team->getStrafen()->count());
        $strafe = new Strafe();
        $strafe
            ->setTeam($team)
            ->setGrund("ABC Grund")
            ->setProzentsatz("")
            ->setVerwarnung('Ja')
            ->setSaison(Config::SAISON);
        $team->getStrafen()->add($strafe);
        TeamRepository::get()->speichern($team);
        $this->assertEquals(expected: 1, actual: $team->getStrafen()->count());

        TeamRepository::get()->deleteStrafe($strafe->getStrafeId());
        $this->assertEquals(expected: 0, actual: $team->getStrafen()->count());
    }

    public function testStrafeMitTurnier(): void
    {
        $turnier = TurnierRepository::get()->getErgebnisTurniere(Config::SAISON - 1)->first();
        $team = self::provideTeam();
        $this->assertEquals(expected: 0, actual: $team->getStrafen()->count());
        $strafe = new Strafe();
        $strafe
            ->setTeam($team)
            ->setGrund("ABC Grund")
            ->setProzentsatz("2")
            ->setVerwarnung('Ja')
            ->setSaison(Config::SAISON)
            ->setTurnier($turnier);
        $team->getStrafen()->add($strafe);
        TeamRepository::get()->speichern($team);
        $this->assertEquals(expected: 1, actual: $team->getStrafen()->count());

        TeamRepository::get()->deleteStrafe($strafe->getStrafeId());
        $this->assertEquals(expected: 0, actual: $team->getStrafen()->count());
    }

}
