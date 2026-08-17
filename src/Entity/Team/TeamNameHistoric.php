<?php

namespace App\Entity\Team;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "teams_name_historic", indexes: [new ORM\Index(name: "team_id", columns: ["team_id"])])]
class TeamNameHistoric
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "saison", type: "integer", nullable: false)]
    private int $saison;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: nTeam::class)]
    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id", nullable: false)]
    private nTeam $team;

    #[ORM\Column(name: "name", type: "string", length: 50, nullable: true)]
    private ?string $name;

    public function getSaison(): int
    {
        return $this->saison;
    }

    public function setSaison(int $saison): self
    {
        $this->saison = $saison;
        return $this;
    }

    public function getTeam(): nTeam
    {
        return $this->team;
    }

    public function setTeam(nTeam $team): self
    {
        $this->team = $team;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): self
    {
        $this->name = $name;
        return $this;
    }

}
