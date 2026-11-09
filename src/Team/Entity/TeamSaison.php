<?php

namespace App\Team\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Saisonzugehörigkeit eines Ligateams: Das Team war in dieser Saison Ligateam und hieß so.
 *
 * Einträge werden nie gelöscht, wenn ein Team die Liga verlässt. Die laufende Saison wird über
 * TeamRepository::saisonzugehoerigkeitAbgleichen() aktuell gehalten.
 */
#[ORM\Entity]
#[ORM\Table(name: "teams_saison", indexes: [new ORM\Index(name: "team_id", columns: ["team_id"])])]
class TeamSaison
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "saison", type: "integer", nullable: false)]
    private int $saison;

    #[ORM\Id]
    #[ORM\ManyToOne(targetEntity: Team::class, inversedBy: "saisons")]
    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id", nullable: false)]
    private Team $team;

    #[ORM\Column(name: "name", type: "string", length: 255, nullable: false)]
    private string $name;

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

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }
}
