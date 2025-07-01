<?php

namespace App\Entity\Spielplan;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: "spielplan_details",
)]
class SpielplanDetails
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Column(name: "spielplan", type: "string", length: 30, nullable: false)]
    private string $spielplan;

    /**
     * ACHTUNG: Hier liegt eine ManyToMany-Beziehung zu SpielplanPaarungen vor. Doctrine 3 kann diese nur mit einem
     * dritten JoinTable verbinden, nicht aber mit dem aktuellen DB-Schema.
     * Lösungsmöglichkeiten:
     * 1. JoinTable einfügen (BestPractice, aber unnötig komplex)
     * 2. Programmatisch mit einem Service SpielplanService::getSpielplanPaarungen
     * 3. Zu OnToMany umbauen
     */
    #[ORM\Column(name: "spielplan_paarung", type: "string", length: 30, nullable: false)]
    private string $spielplanPaarung;

    #[ORM\Column(name: "plaetze", type: "smallint", nullable: true)]
    private int $plaetze;

    #[ORM\Column(name: "anzahl_halbzeiten", type: "smallint", nullable: true)]
    private int $anzahlHalbzeiten;

    #[ORM\Column(name: "halbzeit_laenge", type: "smallint", nullable: true)]
    private int $halbzeitLaenge;

    #[ORM\Column(name: "puffer", type: "smallint", nullable: true)]
    private int $puffer;

    #[ORM\Column(
        name: "pausen",
        type: "string",
        length: 30,
        nullable: true,
        options: ["comment" => "nach Spiel,Minuten#next"]
    )]
    private string $pausen;

    #[ORM\Column(name: "faktor", type: "decimal", precision: 3, scale: 2, nullable: false)]
    private float $faktor;

    public function getSpielplan(): ?string
    {
        return $this->spielplan;
    }

    public function getPlaetze(): int
    {
        return $this->plaetze;
    }

    public function setPlaetze(?bool $plaetze): self
    {
        $this->plaetze = $plaetze;

        return $this;
    }

    public function getAnzahlHalbzeiten(): int
    {
        return $this->anzahlHalbzeiten;
    }

    public function setAnzahlHalbzeiten(?bool $anzahlHalbzeiten): self
    {
        $this->anzahlHalbzeiten = $anzahlHalbzeiten;

        return $this;
    }

    public function getHalbzeitLaenge(): int
    {
        return $this->halbzeitLaenge;
    }

    public function setHalbzeitLaenge(?bool $halbzeitLaenge): self
    {
        $this->halbzeitLaenge = $halbzeitLaenge;

        return $this;
    }

    public function getPuffer(): int
    {
        return $this->puffer;
    }

    public function setPuffer(?bool $puffer): self
    {
        $this->puffer = $puffer;

        return $this;
    }

    public function getPausen(): ?string
    {
        return $this->pausen;
    }

    public function setPausen(?string $pausen): self
    {
        $this->pausen = $pausen;

        return $this;
    }

    public function getFaktor(): float
    {
        return $this->faktor;
    }

    public function setFaktor(float $faktor): self
    {
        $this->faktor = $faktor;
        return $this;
    }

    public function getSpielplanPaarung(): string
    {
        return $this->spielplanPaarung;
    }

    public function setSpielplanPaarung(string $spielplanPaarung): self
    {
        $this->spielplanPaarung = $spielplanPaarung;
        return $this;
    }


}
