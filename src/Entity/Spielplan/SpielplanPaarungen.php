<?php

namespace App\Entity\Spielplan;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "spielplan_paarungen")]
class SpielplanPaarungen
{

    /**
     * Hinweise zur ManyToMany-Beziehung in SpielplanDetails::class
     */
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Id]
    #[ORM\Column(name: "spielplan_paarung", type: "string", length: 30, nullable: false)]
    private string $spielplanPaarung;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "spiel_id", type: "integer", nullable: false)]
    private int $spielId;

    #[ORM\Column(name: "team_a", type: "integer", nullable: false)]
    private int $teamA;

    #[ORM\Column(name: "team_b", type: "integer", nullable: false)]
    private int $teamB;

    #[ORM\Column(name: "schiri_a", type: "integer", nullable: false)]
    private int $schiriA;

    #[ORM\Column(name: "schiri_b", type: "integer", nullable: false)]
    private int $schiriB;

    public function getSpielplanPaarung(): string
    {
        return $this->spielplanPaarung;
    }

    public function setSpielplanPaarung(string $spielplanPaarung): SpielplanPaarungen
    {
        $this->spielplanPaarung = $spielplanPaarung;
        return $this;
    }

    public function getSpielId(): int
    {
        return $this->spielId;
    }

    public function setSpielId(int $spielId): SpielplanPaarungen
    {
        $this->spielId = $spielId;
        return $this;
    }

    public function isTeamA(): bool
    {
        return $this->teamA;
    }

    public function setTeamA(bool $teamA): SpielplanPaarungen
    {
        $this->teamA = $teamA;
        return $this;
    }

    public function getTeamB(): int
    {
        return $this->teamB;
    }

    public function setTeamB(int $teamB): SpielplanPaarungen
    {
        $this->teamB = $teamB;
        return $this;
    }

    public function getSchiriA(): int
    {
        return $this->schiriA;
    }

    public function setSchiriA(int $schiriA): SpielplanPaarungen
    {
        $this->schiriA = $schiriA;
        return $this;
    }

    public function getSchiriB(): int
    {
        return $this->schiriB;
    }

    public function setSchiriB(int $schiriB): SpielplanPaarungen
    {
        $this->schiriB = $schiriB;
        return $this;
    }
}
