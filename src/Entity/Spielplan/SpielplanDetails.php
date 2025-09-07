<?php

namespace App\Entity\Spielplan;

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

    #[ORM\Column(name: "faktor", type: "integer", nullable: false, options: ["comment" => "Nur Nenner"])]
    private int $faktor;

    public function getSpielplan(): ?string
    {
        return $this->spielplan;
    }

    public function isPlaetze(): ?bool
    {
        return $this->plaetze;
    }

    public function setPlaetze(?bool $plaetze): self
    {
        $this->plaetze = $plaetze;

        return $this;
    }

    public function isAnzahlHalbzeiten(): ?bool
    {
        return $this->anzahlHalbzeiten;
    }

    public function setAnzahlHalbzeiten(?bool $anzahlHalbzeiten): self
    {
        $this->anzahlHalbzeiten = $anzahlHalbzeiten;

        return $this;
    }

    public function isHalbzeitLaenge(): ?bool
    {
        return $this->halbzeitLaenge;
    }

    public function setHalbzeitLaenge(?bool $halbzeitLaenge): self
    {
        $this->halbzeitLaenge = $halbzeitLaenge;

        return $this;
    }

    public function isPuffer(): ?bool
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

    public function isFaktor(): ?bool
    {
        return $this->faktor;
    }

    public function setFaktor(bool $faktor): self
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
