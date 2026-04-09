<?php

namespace App\Entity\Team;

use App\Entity\Team\nTeam;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "teams_names", uniqueConstraints: [
        new ORM\UniqueConstraint(name: "team_saison_unique", columns: ["team_id", "saison"]),
        new ORM\UniqueConstraint(name: "name_saison_unique", columns: ["name", "saison"])
    ])]
class Names
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private int $id;

    #[ORM\ManyToOne(targetEntity: nTeam::class, inversedBy: "names")]
    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id", nullable: false)]
    private nTeam $team;

    #[ORM\Column]
    private string $saison;

    #[ORM\Column]
    private string $name;

    public function getName(): string
    {
        return $this->name;
    }
}