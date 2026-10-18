<?php

namespace App\Entity\Abstimmung;

use App\Entity\Team\Team;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "abstimmung_teams")]
class AbstimmungTeam
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: Team::class)]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'team_id', nullable: false)]
    private Team $team;

    #[ORM\Column(type: 'integer')]
    private int $aenderungen;

    public function getTeam(): Team
    {
        return $this->team;
    }

    public function setTeam(Team $team): self
    {
        $this->team = $team;
        return $this;
    }

    public function getAenderungen(): int
    {
        return $this->aenderungen;
    }

    public function setAenderungen(int $aenderungen): self
    {
        $this->aenderungen = $aenderungen;
        return $this;
    }

    public function addAenderung(int $increment): self
    {
        $this->aenderungen += $increment;
        return $this;
    }
}
