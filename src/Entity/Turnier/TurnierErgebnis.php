<?php

namespace App\Entity\Turnier;

use App\Entity\Team\nTeam;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Turnier\Turnier;

/**
 * TurniereErgebnisse
 *
 */
#[ORM\Entity]
#[ORM\Table(name: "turniere_ergebnisse", indexes: [new ORM\Index(name: "team_id", columns: ["team_id"]), new ORM\Index(name: "turniere_ergebnisse_ibfk_2", columns: ["turnier_id"])])]
class TurnierErgebnis
{
    /**
     * @var int
     *
     */
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id] #[ORM\Column(name: "turnier_ergebnis_id", type: "integer", nullable: false)]
    private $turnierErgebnisId;

    /**
     * @var int|null
     *
     */
    #[ORM\Column(name: "ergebnis", type: "integer", nullable: true)]
    private $ergebnis;

    /**
     * @var int
     *
     */
    #[ORM\Column(name: "platz", type: "integer", nullable: false)]
    private $platz;

    /**
     * @var nTeam
     *
     */
    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id")]
    #[ORM\OneToOne(targetEntity: nTeam::class)] private nTeam $team;

    /**
     * @var Turnier
     *
     */
    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    #[ORM\ManyToOne(targetEntity: Turnier::class, inversedBy: "turnier")]
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
