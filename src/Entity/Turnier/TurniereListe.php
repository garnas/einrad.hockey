<?php

namespace App\Entity\Turnier;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Team\nTeam;
use DateTime;

#[ORM\Entity]
#[ORM\Table(
    name: "turniere_liste",
    indexes: [
        new ORM\Index(name: "turniere_liste_ibfk_2", columns: ["turnier_id"]),
        new ORM\Index(name: "turniere_liste_ibfk_1", columns: ["team_id"])
    ]
)]
class TurniereListe
{

    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "liste_id", type: "integer", nullable: false)]
    private $listeId;

    #[ORM\Column(name: "freilos_gesetzt_am", type: "datetime", nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTime $freilosGesetztAm;

    public function getFreilosGesetztAm(): DateTime
    {
        return $this->freilosGesetztAm;
    }

    public function setFreilosGesetztAm(DateTime $freilosGesetztAm): TurniereListe
    {
        $this->freilosGesetztAm = $freilosGesetztAm;
        return $this;
    }

    #[ORM\Column(name: "liste", type: "string", length: 0, nullable: false, options: ["default" => "setz"])]
    private string $liste = 'setz';

    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id")]
    #[ORM\ManyToOne(targetEntity: nTeam::class, inversedBy: "turniereListe")]
    private nTeam $team;

    public function getTeam(): nTeam
    {
        return $this->team;
    }

    public function setTeam (nTeam $team): TurniereListe
    {
        $this->team = $team;
        return $this;
    }

    #[ORM\Column(name: "position_warteliste", type: "integer", nullable: true)]
    private ?int $positionWarteliste;

    #[ORM\Column(name: "freilos_gesetzt", type: "string", length: 0, nullable: false, options: ["default" => "Nein"])]
    private string $freilosGesetzt = 'Nein';

    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    #[ORM\ManyToOne(targetEntity: Turnier::class, inversedBy: "liste")]
    private Turnier $turnier;

    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

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