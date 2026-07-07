<?php

namespace App\Entity\TurnierBericht;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Turnier\Turnier;

#[ORM\Entity]
#[ORM\Table(
    name: "spieler_zeitstrafen",
    indexes: [
        new ORM\Index(name: "turnier_id", columns: ["turnier_id"]),
    ],
)]
class SpielerZeitstrafe
{
    #[ORM\Id]
    #[ORM\GeneratedValue('AUTO')]
    #[ORM\Column(name: "zeitstrafe_id", type: "integer")]
    private int $zeitstrafeId;

    #[ORM\ManyToOne(targetEntity: Turnier::class, inversedBy: 'zeitstrafen', fetch: 'LAZY')]
    #[ORM\JoinColumn(name: 'turnier_id', referencedColumnName: 'turnier_id', nullable: false)]
    private Turnier $turnier;

    #[ORM\Column(name: "spieler", type: "string", length: 255, nullable: false)]
    private string $spieler;

    #[ORM\Column(name: "dauer", type: "string", length: 32, nullable: false)]
    private string $dauer;

    #[ORM\Column(name: "team_a", type: "string", length: 255, nullable: false)]
    private string $team_a;

    #[ORM\Column(name: "team_b", type: "string", length: 255, nullable: false)]
    private string $team_b;

    #[ORM\Column(name: "grund", type: "string", length: 255, nullable: false)]
    private string $grund;
    
    public function __construct(Turnier $turnier)
    {
        $this->turnier = $turnier;
    }

    public function getId(): int
    {
        return $this->zeitstrafeId;
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

    public function getDauer(): string
    {
        return $this->dauer;
    }

    public function setDauer(string $dauer): self
    {
        $this->dauer = $dauer;
        return $this;
    }

    public function getTeamA(): string
    {
        return $this->team_a;
    }

    public function setTeamA(string $team_a): self
    {
        $this->team_a = $team_a;
        return $this;
    }

    public function getTeamB(): string
    {
        return $this->team_b;
    }

    public function setTeamB(string $team_b): self
    {
        $this->team_b = $team_b;
        return $this;
    }

    public function getGrund(): string
    {
        return $this->grund;
    }

    public function setGrund(string $grund): self
    {
        $this->grund = $grund;
        return $this;
    }
}
