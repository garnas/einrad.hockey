<?php

namespace App\Entity\Abstimmung;

use App\Entity\Team\nTeam;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "abstimmung_team")]
class AbstimmungTeam
{
    #[ORM\Id]
    #[ORM\OneToOne(targetEntity: nTeam::class)]
    #[ORM\JoinColumn(name: 'team_id', referencedColumnName: 'team_id', nullable: false)]
    private nTeam $team;

    #[ORM\Column(type: 'integer')]
    private int $aenderungen;

    public function getTeam(): nTeam
    {
        return $this->team;
    }

    public function setTeam(nTeam $team): self
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