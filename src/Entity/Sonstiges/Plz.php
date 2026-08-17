<?php

namespace App\Entity\Sonstiges;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "plz")]
class Plz
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "PLZ", type: "string", length: 5, nullable: false)]
    private string $plz;

    #[ORM\Column(name: "Ort", type: "text", nullable: true)]
    private ?string $ort;

    #[ORM\Column(name: "Lon", type: "float", nullable: true)]
    private ?float $lon;

    #[ORM\Column(name: "LAT", type: "float", nullable: true)]
    private ?float $lat;

    public function getPlz(): string
    {
        return $this->plz;
    }

    public function setPlz(string $plz): self
    {
        $this->plz = $plz;
        return $this;
    }

    public function getOrt(): ?string
    {
        return $this->ort;
    }

    public function setOrt(?string $ort): self
    {
        $this->ort = $ort;
        return $this;
    }

    public function getLon(): ?float
    {
        return $this->lon;
    }

    public function setLon(?float $lon): self
    {
        $this->lon = $lon;
        return $this;
    }

    public function getLat(): ?float
    {
        return $this->lat;
    }

    public function setLat(?float $lat): self
    {
        $this->lat = $lat;
        return $this;
    }

}
