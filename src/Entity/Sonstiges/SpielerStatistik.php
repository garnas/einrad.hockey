<?php

namespace App\Entity\Sonstiges;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "spieler_statistik")]
class SpielerStatistik
{
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "id", type: "integer", nullable: false)]
    private int $id;

    #[ORM\Column(name: "date", type: "datetime", nullable: false, options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTime $date;

    #[ORM\Column(name: "saison", type: "integer", nullable: false)]
    private int $saison;

    #[ORM\Column(name: "geschlecht", type: "string", length: 0, nullable: true)]
    private ?string $geschlecht;

    #[ORM\Column(name: "anzahl", type: "integer", nullable: false)]
    private int $anzahl;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDate(): DateTime
    {
        return $this->date;
    }

    public function setDate(DateTime $date): self
    {
        $this->date = $date;
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

    public function getGeschlecht(): ?string
    {
        return $this->geschlecht;
    }

    public function setGeschlecht(?string $geschlecht): self
    {
        $this->geschlecht = $geschlecht;
        return $this;
    }

    public function getAnzahl(): int
    {
        return $this->anzahl;
    }

    public function setAnzahl(int $anzahl): self
    {
        $this->anzahl = $anzahl;
        return $this;
    }

}
