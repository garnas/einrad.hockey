<?php

namespace App\Entity\Turnier;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Team\nTeam;

use DoctrineWrapper;

/**
 * TurniereListe
 *
 * @ORM\Table(name="turniere_liste", indexes={@ORM\Index(name="turniere_liste_ibfk_2", columns={"turnier_id"}), @ORM\Index(name="turniere_liste_ibfk_1", columns={"team_id"})})
 * @ORM\Entity
 */
class TurniereListe
{
    /**
     * @var int
     *
     * @ORM\Column(name="liste_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $listeId;

    /**
     * @var string
     *
     * @ORM\Column(name="liste", type="string", length=0, nullable=false, options={"default"="melde"})
     */
    private string $liste = 'melde';

    /**
     * @var nTeam
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Team\nTeam", inversedBy="turniere_liste")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private nTeam $team;

    /**
     * @return nTeam
     */
    public function getTeam(): nTeam
    {
        return $this->team;
    }

    /**
     * @param nTeam $team
     * @return TurniereListe
     */
    public function setTeam (nTeam $team): TurniereListe
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @var int|null
     *
     * @ORM\Column(name="position_warteliste", type="integer", nullable=true)
     */
    private $positionWarteliste;

    /**
     * @var string
     *
     * @ORM\Column(name="freilos_gesetzt", type="string", length=0, nullable=false, options={"default"="Nein"})
     */
    private $freilosGesetzt = 'Nein';

    /**
     * @var Turnier
     * @ORM\ManyToOne(targetEntity="App\Entity\Turnier\Turnier", inversedBy="turnier")
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private $turnier;

    /**
     * @return Turnier
     */
    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    /**
     * @param Turnier $turnier
     * @return TurniereListe
     */
    public function setTurnier(Turnier $turnier): TurniereListe
    {
        $this->turnier = $turnier;
        return $this;
    }

    public function getListeId(): ?int
    {
        return $this->listeId;
    }

    public function getListe(): ?string
    {
        return $this->liste;
    }

    public function setListe(string $liste): self
    {
        $this->liste = $liste;

        return $this;
    }

    public function getPositionWarteliste(): ?int
    {
        return $this->positionWarteliste;
    }

    public function setPositionWarteliste(?int $positionWarteliste): self
    {
        $name = $this->team->getName();

        $this->turnier->getLogService()->autoLog("Position Warteliste $name", $this->positionWarteliste, $positionWarteliste);
        $this->positionWarteliste = $positionWarteliste;

        return $this;
    }

    public function getFreilosGesetzt(): ?string
    {
        return $this->freilosGesetzt;
    }

    public function setFreilosGesetzt(string $freilosGesetzt): self
    {
        $this->freilosGesetzt = $freilosGesetzt;

        return $this;
    }

}