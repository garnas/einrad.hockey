<?php

namespace App\Entity\Team;

use App\Entity\Turnier\Turnier;
use Config;
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
     * @var DateTime|null
     *
     * @ORM\Column(name="gesetzt_am", type="datetime", nullable=true, options={"default"="current_timestamp(1)"})
     */
    private ?DateTime $gesetztAm;

    /**
     * @return DateTime|null
     */
    public function getGesetztAm(): ?DateTime
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
     * @var string
     *
     * @ORM\Column(name="grund", type="string", nullable=false)
     */
    private string $grund;

    /**
     * @var int
     *
     * @ORM\Column(name="saison", type="integer", nullable=false)
     */
    private int $saison = Config::SAISON;

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
     * @var Turnier|null
     * @ORM\OneToOne(targetEntity="App\Entity\Turnier\Turnier")
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private ?Turnier $turnier;

    /**
     * @return Turnier|null
     */
    public function getTurnier(): ?Turnier
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
        return $this->turnier && $this->getGesetztAm();
    }

    public function setzen(Turnier $turnier): void
    {
        $this->setTurnier($turnier);
        $this->setGesetztAm();
    }

    public function setGrund(FreilosGrund $grund): Freilos
    {
        $this->grund = $grund->name;
        return $this;
    }

    public function getGrund(): FreilosGrund
    {
        return FreilosGrund::fromName($this->grund);
    }

    public function setSaison(int $saison): Freilos
    {
        $this->saison = $saison;
        return $this;
    }

    public function isGueltig(): bool
    {
        return $this->saison >= Config::SAISON -1;
    }

    public function getSaison(): int
    {
        return $this->saison;
    }

    public function id(): int
    {
        return $this->freilosId;
    }

}