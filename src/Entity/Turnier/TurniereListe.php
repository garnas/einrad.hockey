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
    private $liste = 'melde';

    /**
     * @var nTeam
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Team\nTeam", inversedBy="turniere_liste")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private nTeam $teamsLiga;

    /**
     * @return nTeam
     */
    public function get_team(): nTeam
    {
        return $this->teamsLiga;
    }

    /**
     * @param nTeam $teamsLiga
     */
    public function set_team(nTeam $teamsLiga): void
    {
        $this->teamsLiga = $teamsLiga;
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
     * @var int
     *
     * @ORM\Column(name="turnier_id", type="integer")
     */
    private int $turnierId;

    /**
     * @return int
     */
    public function get_turnier_id(): int
    {
        return $this->turnierId;
    }

    /**
     * @param int $turnierId
     */
    public function set_turnier_id(int $turnierId): void
    {
        $this->turnierId = $turnierId;
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