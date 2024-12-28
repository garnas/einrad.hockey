<?php

namespace App\Entity\Spielplan;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpielplanDetails
 *
 */
#[ORM\Entity] #[ORM\Table(name: "spielplan_details", indexes: [new ORM\Index(name: "spielplan_paarung", columns: ["spielplan_paarung"])])] class SpielplanDetails
{
    #[ORM\Id] #[ORM\GeneratedValue(strategy: "IDENTITY")] #[ORM\Column(name: "spielplan", type: "string", length: 30, nullable: false)] private $spielplan;

    #[ORM\Column(name: "plaetze", type: "boolean", nullable: true)] private $plaetze;

    #[ORM\Column(name: "anzahl_halbzeiten", type: "boolean", nullable: true)] private $anzahlHalbzeiten;

    #[ORM\Column(name: "halbzeit_laenge", type: "boolean", nullable: true)] private $halbzeitLaenge;

    #[ORM\Column(name: "puffer", type: "boolean", nullable: true)] private $puffer;

    #[ORM\Column(name: "pausen", type: "string", length: 30, nullable: true, options: ["comment" => "nach Spiel,Minuten#next"])] private $pausen;

    #[ORM\Column(name: "faktor", type: "boolean", nullable: false, options: ["comment" => "Nur Nenner"])] private $faktor;

    #[ORM\ManyToOne(targetEntity: "SpielplanPaarungen")]
    #[ORM\JoinColumn(name: "spielplan_paarung", referencedColumnName: "spielplan_paarung")]
    private SpielplanPaarungen $spielplanPaarung;


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

    public function getSpielplanPaarung(): ?SpielplanPaarungen
    {
        return $this->spielplanPaarung;
    }

    public function setSpielplanPaarung(?SpielplanPaarungen $spielplanPaarung): self
    {
        $this->spielplanPaarung = $spielplanPaarung;

        return $this;
    }


}
