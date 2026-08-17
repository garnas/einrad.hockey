<?php

namespace App\Entity\Turnier;

use App\Entity\Team\nTeam;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: "spiele",
    indexes: [
        new ORM\Index(name: "team_id_a", columns: ["team_id_a"]),
        new ORM\Index(name: "team_id_b", columns: ["team_id_b"]),
        new ORM\Index(name: "schiri_team_id_a", columns: ["schiri_team_id_a"]),
        new ORM\Index(name: "schiri_team_id_b", columns: ["schiri_team_id_b"]),
    ],
)]
class Spiel
{
    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Turnier::class)]
    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id", nullable: false)]
    private Turnier $turnier;

    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "spiel_id", type: "integer", nullable: false)]
    private int $spielId;

    #[ORM\ManyToOne(targetEntity: nTeam::class)]
    #[ORM\JoinColumn(name: "team_id_a", referencedColumnName: "team_id", nullable: false)]
    private nTeam $teamA;

    #[ORM\ManyToOne(targetEntity: nTeam::class)]
    #[ORM\JoinColumn(name: "team_id_b", referencedColumnName: "team_id", nullable: false)]
    private nTeam $teamB;

    #[ORM\ManyToOne(targetEntity: nTeam::class)]
    #[ORM\JoinColumn(name: "schiri_team_id_a", referencedColumnName: "team_id", nullable: false)]
    private nTeam $schiriTeamA;

    #[ORM\ManyToOne(targetEntity: nTeam::class)]
    #[ORM\JoinColumn(name: "schiri_team_id_b", referencedColumnName: "team_id", nullable: false)]
    private nTeam $schiriTeamB;

    #[ORM\Column(name: "tore_a", type: "integer", nullable: true)]
    private ?int $toreA;

    #[ORM\Column(name: "tore_b", type: "integer", nullable: true)]
    private ?int $toreB;

    #[ORM\Column(name: "penalty_a", type: "integer", nullable: true)]
    private ?int $penaltyA;

    #[ORM\Column(name: "penalty_b", type: "integer", nullable: true)]
    private ?int $penaltyB;

    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    public function setTurnier(Turnier $turnier): self
    {
        $this->turnier = $turnier;
        return $this;
    }

    public function getSpielId(): int
    {
        return $this->spielId;
    }

    public function setSpielId(int $spielId): self
    {
        $this->spielId = $spielId;
        return $this;
    }

    public function getTeamA(): nTeam
    {
        return $this->teamA;
    }

    public function setTeamA(nTeam $teamA): self
    {
        $this->teamA = $teamA;
        return $this;
    }

    public function getTeamB(): nTeam
    {
        return $this->teamB;
    }

    public function setTeamB(nTeam $teamB): self
    {
        $this->teamB = $teamB;
        return $this;
    }

    public function getSchiriTeamA(): nTeam
    {
        return $this->schiriTeamA;
    }

    public function setSchiriTeamA(nTeam $schiriTeamA): self
    {
        $this->schiriTeamA = $schiriTeamA;
        return $this;
    }

    public function getSchiriTeamB(): nTeam
    {
        return $this->schiriTeamB;
    }

    public function setSchiriTeamB(nTeam $schiriTeamB): self
    {
        $this->schiriTeamB = $schiriTeamB;
        return $this;
    }

    public function getToreA(): ?int
    {
        return $this->toreA;
    }

    public function setToreA(?int $toreA): self
    {
        $this->toreA = $toreA;
        return $this;
    }

    public function getToreB(): ?int
    {
        return $this->toreB;
    }

    public function setToreB(?int $toreB): self
    {
        $this->toreB = $toreB;
        return $this;
    }

    public function getPenaltyA(): ?int
    {
        return $this->penaltyA;
    }

    public function setPenaltyA(?int $penaltyA): self
    {
        $this->penaltyA = $penaltyA;
        return $this;
    }

    public function getPenaltyB(): ?int
    {
        return $this->penaltyB;
    }

    public function setPenaltyB(?int $penaltyB): self
    {
        $this->penaltyB = $penaltyB;
        return $this;
    }

}
