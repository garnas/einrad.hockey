<?php

namespace App\Entity\Sonstiges;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "ligakarte_gesuch")]
class LigakarteGesuch
{
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "gesuch_id", type: "integer", nullable: false)]
    private int $gesuchId;

    #[ORM\Column(name: "plz", type: "integer", nullable: false)]
    private int $plz;

    #[ORM\Column(name: "ort", type: "string", length: 255, nullable: false)]
    private string $ort;

    #[ORM\Column(name: "LAT", type: "float", nullable: false)]
    private float $lat;

    #[ORM\Column(name: "Lon", type: "float", nullable: false)]
    private float $lon;

    #[ORM\Column(name: "r_name", type: "string", length: 255, nullable: false)]
    private string $rName;

    #[ORM\Column(name: "kontakt", type: "string", length: 255, nullable: false)]
    private string $kontakt;

    #[ORM\Column(name: "zeit", type: "datetime", nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTime $zeit;

    public function getGesuchId(): ?int
    {
        return $this->gesuchId;
    }

    public function getPlz(): int
    {
        return $this->plz;
    }

    public function setPlz(int $plz): self
    {
        $this->plz = $plz;
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

    public function getLat(): float
    {
        return $this->lat;
    }

    public function setLat(float $lat): self
    {
        $this->lat = $lat;
        return $this;
    }

    public function getLon(): float
    {
        return $this->lon;
    }

    public function setLon(float $lon): self
    {
        $this->lon = $lon;
        return $this;
    }

    public function getRName(): string
    {
        return $this->rName;
    }

    public function setRName(string $rName): self
    {
        $this->rName = $rName;
        return $this;
    }

    public function getKontakt(): string
    {
        return $this->kontakt;
    }

    public function setKontakt(string $kontakt): self
    {
        $this->kontakt = $kontakt;
        return $this;
    }

    public function getZeit(): DateTime
    {
        return $this->zeit;
    }

    public function setZeit(DateTime $zeit): self
    {
        $this->zeit = $zeit;
        return $this;
    }

}
