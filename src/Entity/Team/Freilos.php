<?php

namespace App\Entity\Team;

use App\Entity\Turnier\Turnier;
use Config;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use DoctrineWrapper;

#[ORM\Entity] #[ORM\Table(name: "freilose", indexes: [new ORM\Index(columns: ["turnier_id"], name: "freilose_ibfk_2"), new ORM\Index(columns: ["team_id"], name: "freilose_ibfk_1")])] class Freilos
{

    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "freilos_id", type: "integer", nullable: false)] private int $freilosId;

    #[ORM\Column(name: "gesetzt_am", type: "datetime", nullable: true, options: ["default" => "current_timestamp(1)"])] private ?DateTime $gesetztAm;

    public function getGesetztAm(): ?DateTime
    {
        return $this->gesetztAm;
    }

    public function setGesetztAm(DateTime $gesetztAm = new DateTime()): Freilos
    {
        $this->gesetztAm = $gesetztAm;
        return $this;
    }

    #[ORM\Column(name: "erstellt_am", type: "datetime", nullable: false, options: ["default" => "current_timestamp(1)"])] private DateTime $erstelltAm;

    public function getErstelltAm(): DateTime
    {
        return $this->erstelltAm;
    }

    public function setErstelltAm(DateTime $erstelltAm = new DateTime()): Freilos
    {
        $this->erstelltAm = $erstelltAm;
        return $this;
    }

    #[ORM\Column(name: "grund", type: "string", nullable: false)] private string $grund;

    #[ORM\Column(name: "saison", type: "integer", nullable: false)] private int $saison = Config::SAISON;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Team\\nTeam", inversedBy: "freilos")]
    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id")]
    private nTeam $team;

    public function getTeam(): nTeam
    {
        return $this->team;
    }

    public function setTeam (nTeam $team): Freilos
    {
        $this->team = $team;
        return $this;
    }

    #[ORM\OneToOne(targetEntity: "App\Entity\Turnier\Turnier")] #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")] private ?Turnier $turnier;

    public function getTurnier(): ?Turnier
    {
        return $this->turnier;
    }

    public function setTurnier(Turnier $turnier): Freilos
    {
        $this->turnier = $turnier;
        return $this;
    }

    public function isGesetzt(): bool
    {
        return isset($this->turnier) && $this->getGesetztAm() !== null;
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