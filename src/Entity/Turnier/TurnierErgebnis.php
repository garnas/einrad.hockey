<?php

namespace App\Entity\Turnier;

use App\Entity\Team\nTeam;
use Doctrine\ORM\Mapping as ORM;

/**
 * TurniereErgebnisse
 *
 * @ORM\Table(name="turniere_ergebnisse", indexes={@ORM\Index(name="team_id", columns={"team_id"}), @ORM\Index(name="turniere_ergebnisse_ibfk_2", columns={"turnier_id"})})
 * @ORM\Entity
 */
class TurnierErgebnis
{
    /**
     * @var int
     *
     * @ORM\Column(name="turnier_ergebnis_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $turnierErgebnisId;

    /**
     * @var int|null
     *
     * @ORM\Column(name="ergebnis", type="integer", nullable=true)
     */
    private $ergebnis;

    /**
     * @var int
     *
     * @ORM\Column(name="platz", type="integer", nullable=false)
     */
    private $platz;

    /**
     * @var nTeam
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Team\nTeam")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private nTeam $team;

    /**
     * @var Turnier
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Turnier\Turnier", inversedBy="turnier")
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
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
