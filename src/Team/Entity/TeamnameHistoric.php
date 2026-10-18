<?php

namespace App\Team\Entity;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "teams_name_historic", indexes: [new ORM\Index(name: "team_id", columns: ["team_id"])])]
class TeamnameHistoric
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "saison", type: "integer", nullable: false)]
    private int $saison;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id", nullable: false)]
    private Team $team;

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

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
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
