<?php

namespace App\Entity\Team;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use DoctrineWrapper;

/**
 * Freilos
 *
 * @ORM\Table(name="freilose", indexes={@ORM\Index(name="freilose_ibfk_2", columns={"turnier_id"}), @ORM\Index(name="freilose_ibfk_1", columns={"team_id"})})
 * @ORM\Entity
 */
class Freilos
{
    /**
     * @var int
     *
     * @ORM\Column(name="freilos_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $freilosId;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="gesetzt_am", type="datetime", nullable=false, options={"default"="current_timestamp(1)"})
     */
    private DateTime $gesetztAm;

    /**
     * @return DateTime
     */
    public function getGesetztAm(): DateTime
    {
        return $this->gesetztAm;
    }

    /**
     * @param DateTime $gesetztAm
     * @return Freilos
     */
    public function setGesetztAm(DateTime $gesetztAm = new DateTime()): Freilos
    {
        $this->gesetztAm = $gesetztAm;
        return $this;
    }

    /**
     * @var DateTime
     *
     * @ORM\Column(name="erstellt_am", type="datetime", nullable=false, options={"default"="current_timestamp(1)"})
     */
    private DateTime $erstelltAm;

    /**
     * @return DateTime
     */
    public function getErstelltAm(): DateTime
    {
        return $this->erstelltAm;
    }

    /**
     * @param DateTime $erstelltAm
     * @return Freilos
     */
    public function setErstelltAm(DateTime $erstelltAm = new DateTime()): Freilos
    {
        $this->erstelltAm = $erstelltAm;
        return $this;
    }

    /**
     * @var String
     *
     * @ORM\Column(name="grund", type="string", nullable=false)
     */
    private string $grund;

    /**
     * @var nTeam
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Team\nTeam", inversedBy="freilos")
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
    public function setTeam (nTeam $team): Freilos
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @var Turnier
     * @ORM\ManyToOne(targetEntity="App\Entity\Turnier\Turnier", inversedBy="liste")
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private Turnier $turnier;

    /**
     * @return Turnier
     */
    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    /**
     * @param Turnier $turnier
     * @return Freilos
     */
    public function setTurnier(Turnier $turnier): Freilos
    {
        $this->turnier = $turnier;
        return $this;
    }

    public function isGesetzt(): bool
    {
        return (bool) $this->turnier;
    }

    public function setzen(Turnier $turnier): void
    {
        $this->setTurnier($turnier);
        $this->setGesetztAm();
    }

    public function setGrund(string $grund): Freilos
    {
        $this->grund = $grund;
        return $this;
    }

    public function getGrund(): string
    {
        return $this->grund;
    }

}