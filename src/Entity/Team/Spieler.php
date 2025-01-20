<?php

namespace App\Entity\Team;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "spieler", indexes: [new ORM\Index(name: "team_id", columns: ["team_id"])])]
class Spieler
{

    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "spieler_id", type: "integer", nullable: false)]
    private int $spielerId;

    #[ORM\Column(name: "vorname", type: "string", length: 255, nullable: false)]
    private string $vorname;

    #[ORM\Column(name: "nachname", type: "string", length: 255, nullable: false)]
    private string $nachname;

    #[ORM\Column(name: "jahrgang", type: "integer", nullable: false)]
    private int $jahrgang;

    #[ORM\Column(name: "geschlecht", type: "string", length: 0, nullable: false)]
    private string $geschlecht;

    #[ORM\Column(name: "schiri", type: "integer", nullable: true)]
    private ?int $schiri;

    #[ORM\Column(name: "junior", type: "string", length: 0, nullable: true)]
    private ?string $junior;

    #[ORM\Column(name: "letzte_saison", type: "integer", nullable: true)]
    private ?int $letzteSaison;

    #[ORM\Column(name: "timestamp", type: "datetime", nullable: true)]
    private ?DateTime $timestamp;

    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id")]
    #[ORM\ManyToOne(targetEntity: nTeam::class, inversedBy: "kader")]
    private nTeam $team;

    public function getSpielerId(): ?int
    {
        return $this->spielerId;
    }

    public function getVorname(): ?string
    {
        return $this->vorname;
    }

    public function setVorname(string $vorname): self
    {
        $this->vorname = $vorname;

        return $this;
    }

    public function getNachname(): ?string
    {
        return $this->nachname;
    }

    public function setNachname(string $nachname): self
    {
        $this->nachname = $nachname;

        return $this;
    }

    public function getJahrgang(): ?int
    {
        return $this->jahrgang;
    }

    public function setJahrgang(int $jahrgang): self
    {
        $this->jahrgang = $jahrgang;

        return $this;
    }

    public function getGeschlecht(): ?string
    {
        return $this->geschlecht;
    }

    public function setGeschlecht(string $geschlecht): self
    {
        $this->geschlecht = $geschlecht;

        return $this;
    }

    public function getSchiri(): ?int
    {
        return $this->schiri;
    }

    public function setSchiri(?int $schiri): self
    {
        $this->schiri = $schiri;

        return $this;
    }

    public function getJunior(): ?string
    {
        return $this->junior;
    }

    public function setJunior(?string $junior): self
    {
        $this->junior = $junior;

        return $this;
    }

    public function getLetzteSaison(): ?int
    {
        return $this->letzteSaison;
    }

    public function setLetzteSaison(?int $letzteSaison): self
    {
        $this->letzteSaison = $letzteSaison;

        return $this;
    }

    public function getTimestamp(): ?DateTime
    {
        return $this->timestamp;
    }

    public function setTimestamp(?DateTime $timestamp): self
    {
        $this->timestamp = $timestamp;

        return $this;
    }

    public function getTeam(): ?nTeam
    {
        return $this->team;
    }

    public function setTeam(?nTeam $team): self
    {
        $this->team = $team;

        return $this;
    }


}
