<?php

namespace App\Entity\TurnierBericht;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Turnier\Turnier;
use App\Entity\Team\nTeam;

#[ORM\Entity]
#[ORM\Table(
    name: "spieler_ausleihen",
    indexes: [
        new ORM\Index(name: "turnier_id", columns: ["turnier_id"]),
    ],
)]
class SpielerAusleihe
{
    #[ORM\Id]
    #[ORM\GeneratedValue('AUTO')]
    #[ORM\Column(name: "ausleihe_id", type: "integer")]
    private int $ausleiheId;

    #[ORM\ManyToOne(targetEntity: Turnier::class, inversedBy: 'leihen', fetch: 'LAZY')]
    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    private Turnier $turnier;

    #[ORM\Column(name: "spieler", type: "string", length: 255, nullable: false)]
    private string $spieler;

    #[ORM\Column(name: "team_auf", type: "string", length: 255, nullable: false)]
    private string $team_auf;

    #[ORM\Column(name: "team_ab", type: "string", length: 255, nullable: false)]
    private string $team_ab;


    public function __construct(Turnier $turnier)
    {
        $this->turnier = $turnier;
    }

    public function getId(): int
    {
        return $this->ausleiheId;
    }

    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    public function setTurnier(Turnier $turnier): self
    {
        $this->turnier = $turnier;
        return $this;
    }

    public function getSpieler(): string
    {
        return $this->spieler;
    }

    public function setSpieler(string $spieler): self
    {
        $this->spieler = $spieler;
        return $this;
    }

    public function getTeamAuf(): string
    {
        return $this->team_auf;
    }

    public function setTeamAuf(string $team_auf): self
    {
        $this->team_auf = $team_auf;
        return $this;
    }

    public function getTeamAb(): string
    {
        return $this->team_ab;
    }

    public function setTeamAb(string $team_ab): self
    {
        $this->team_ab = $team_ab;
        return $this;
    }

}
