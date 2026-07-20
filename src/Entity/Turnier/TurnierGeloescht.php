<?php

namespace App\Entity\Turnier;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "turniere_geloescht")]
class TurnierGeloescht
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "turnier_id", type: "integer", nullable: false)]
    private int $turnierId;

    #[ORM\Column(name: "datum", type: "date", nullable: false)]
    private DateTime $datum;

    #[ORM\Column(name: "ort", type: "string", length: 255, nullable: false)]
    private string $ort;

    #[ORM\Column(name: "grund", type: "string", length: 255, nullable: false)]
    private string $grund;

    #[ORM\Column(name: "saison", type: "integer", nullable: false)]
    private int $saison;

    public function getTurnierId(): int
    {
        return $this->turnierId;
    }

    public function setTurnierId(int $turnierId): self
    {
        $this->turnierId = $turnierId;
        return $this;
    }

    public function getDatum(): DateTime
    {
        return $this->datum;
    }

    public function setDatum(DateTime $datum): self
    {
        $this->datum = $datum;
        return $this;
    }

    public function getOrt(): string
    {
        return $this->ort;
    }

    public function setOrt(string $ort): self
    {
        $this->ort = $ort;
        return $this;
    }

    public function getGrund(): string
    {
        return $this->grund;
    }

    public function setGrund(string $grund): self
    {
        $this->grund = $grund;
        return $this;
    }

    public function getSaison(): int
    {
        return $this->saison;
    }

    public function setSaison(int $saison): self
    {
        $this->saison = $saison;
        return $this;
    }

}
