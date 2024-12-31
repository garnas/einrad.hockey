<?php

namespace App\Entity\Turnier;

use App\Entity\Team\nTeam;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "turniere_ergebnisse", indexes: [new ORM\Index(columns: ["team_id"], name: "team_id"), new ORM\Index(columns: ["turnier_id"], name: "turniere_ergebnisse_ibfk_2")])]
class TurnierErgebnis
{

    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "turnier_ergebnis_id", type: "integer", nullable: false)]
    private $turnierErgebnisId;

    #[ORM\Column(name: "ergebnis", type: "integer", nullable: true)]
    private int $ergebnis;

    #[ORM\Column(name: "platz", type: "integer", nullable: false)]
    private int $platz;

    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id")]
    #[ORM\OneToOne(targetEntity: nTeam::class)]
    private nTeam $team;

    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    #[ORM\ManyToOne(targetEntity: Turnier::class, inversedBy: "ergebnis")]
    private Turnier $turnier;

    public function getTurnierErgebnisId(): ?int
    {
        return $this->turnierErgebnisId;
    }

    public function getErgebnis(): ?int
    {
        return $this->ergebnis;
    }

    public function setErgebnis(?int $ergebnis): self
    {
        $this->ergebnis = $ergebnis;

        return $this;
    }

    public function getPlatz(): ?int
    {
        return $this->platz;
    }

    public function setPlatz(int $platz): self
    {
        $this->platz = $platz;

        return $this;
    }

    public function getTurnier(): ?Turnier
    {
        return $this->turnier;
    }

    public function setTurnier(?Turnier $turnier): self
    {
        $this->turnier = $turnier;

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


}
