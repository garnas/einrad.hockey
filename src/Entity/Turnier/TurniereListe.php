<?php

namespace App\Entity\Turnier;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Team\nTeam;
use DoctrineWrapper;
use DateTime;
use App\Entity\Turnier\Turnier;

/**
 * TurniereListe
 *
 */
#[ORM\Entity]
#[ORM\Table(name: "turniere_liste", indexes: [new ORM\Index(name: "turniere_liste_ibfk_2", columns: ["turnier_id"]), new ORM\Index(name: "turniere_liste_ibfk_1", columns: ["team_id"])])]
class TurniereListe
{
    /**
     * @var int
     */
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id] #[ORM\Column(name: "liste_id", type: "integer", nullable: false)]
    private $listeId;

    /**
     * @var DateTime
     *
     */
    #[ORM\Column(name: "freilos_gesetzt_am", type: "datetime", nullable: false, options: ["default" => "current_timestamp(1)"])]
    private DateTime $freilosGesetztAm;

    /**
     * @return DateTime
     */
    public function getFreilosGesetztAm(): DateTime
    {
        return $this->freilosGesetztAm;
    }

    /**
     * @param DateTime $freilosGesetztAm
     * @return TurniereListe
     */
    public function setFreilosGesetztAm(DateTime $freilosGesetztAm): TurniereListe
    {
        $this->freilosGesetztAm = $freilosGesetztAm;
        return $this;
    }

    /**
     * @var string
     *
     */
    #[ORM\Column(name: "liste", type: "string", length: 0, nullable: false, options: ["default" => "setz"])]
    private string $liste = 'setz';

    /**
     * @var nTeam
     *
     */
    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id")]
    #[ORM\ManyToOne(targetEntity: nTeam::class, inversedBy: "turniereListe")]
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
     */
    #[ORM\Column(name: "position_warteliste", type: "integer", nullable: true)]
    private ?int $positionWarteliste;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: "freilos_gesetzt", type: "string", length: 0, nullable: false, options: ["default" => "Nein"])]
    private string $freilosGesetzt = 'Nein';

    /**
     * @var Turnier
     */
    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    #[ORM\ManyToOne(targetEntity: Turnier::class, inversedBy: "liste")]
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

    public function isWarteliste(): bool
    {
        return $this->getListe() === "warteliste";
    }

    public function isSetzliste(): bool
    {
        return $this->getListe() === "setzliste";
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

    public function hasFreilosGesetzt(): bool
    {
        return $this->freilosGesetzt === 'Ja';
    }

    public function setFreilosGesetzt(string $freilosGesetzt): self
    {
        $this->freilosGesetzt = $freilosGesetzt;
        return $this;
    }

}