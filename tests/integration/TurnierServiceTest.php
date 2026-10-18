<?php

namespace integration;

use App\Team\Entity\Team;
use App\Team\Entity\Teamdetails;
use App\Team\Repository\TeamRepository;
use App\Team\Service\TeamService;
use PHPUnit\Framework\TestCase;

class TurnierServiceTest extends TestCase
{
    protected function provideTeamForLogin($teamname, $ligateam, $aktiv, $password)
    {
        $team = TeamRepository::get()->findByName($teamname);
        if ($team) {
            TeamRepository::get()->delete($team);
        }
        $team = (new Team())
            ->setName($teamname)
            ->setLigateam($ligateam)
            ->setPasswort($password)
            ->setAktiv($aktiv);
        $team->setDetails(
            (new Teamdetails())
                ->setTeam($team)
                ->setTeamfoto(null)->setTrikotFarbe1(null)->setTrikotFarbe2(null),
        );

        TeamRepository::get()->speichern($team);
    }

    public function testLoginTeamUnbekannt(): void
    {
        unset($_SESSION);
        $isSuccess = TeamService::login("nicht-existierender-Teamname'\"\\123455--#//?%", "");
        $this->assertFalse($isSuccess);
        $this->assertEquals(expected: "Falscher Loginname", actual: $_SESSION["messages"][0]["text"]);
    }

    public function testLoginNichtligateam(): void
    {
        unset($_SESSION);
        self::provideTeamForLogin(teamname: "Int-Test", ligateam: "Nein", aktiv: "Ja", password: "test");
        $isSuccess = TeamService::login("Int-Test", "test");
        $this->assertFalse($isSuccess);
        $this->assertEquals(expected: "Falscher Loginname", actual: $_SESSION["messages"][0]["text"]);
    }

    public function testLoginInaktivesLigateam(): void
    {
        unset($_SESSION);
        self::provideTeamForLogin(teamname: "Int-Test", ligateam: "Ja", aktiv: "Nein", password: "0!\"\\§$%&/()=*'-.,--//?");
        $isSuccess = TeamService::login("Int-Test", "0!\"\\§$%&/()=*'-.,--//?");
        $this->assertFalse($isSuccess);
    }

    public function testLoginLigateamSuccess(): void
    {
        unset($_SESSION);
        self::provideTeamForLogin(teamname: "Int-Test", ligateam: "Ja", aktiv: "Ja", password: "0!\"\\§$%&/()=*'-.,--//?");
        $isSuccess = TeamService::login("Int-Test", "0!\"\\§$%&/()=*'-.,--//?");
        $this->assertTrue($isSuccess);
    }

    public function testLoginLigateamFail(): void
    {
        unset($_SESSION);
        self::provideTeamForLogin(teamname: "Int-Test", ligateam: "Ja", aktiv: "Ja", password: "0!\"\\§$%&/()=*'-.,--//?");
        $isSuccess = TeamService::login("Int-Test", "falsches-pw");
        $this->assertFalse($isSuccess);
    }
}
