<?php

namespace App\Entity\Turnier;

use Doctrine\ORM\Mapping as ORM;

/**
 * TurniereBerichte
 *
 */
#[ORM\Entity] #[ORM\Table(name: "turniere_berichte", indexes: [new ORM\Index(columns: ["turnier_id"], name: "turnier_id")])] class TurnierBericht
{

    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")] #[ORM\OneToOne(mappedBy: "turnier", targetEntity: "App\Entity\Turnier\Turnier")] #[ORM\Id] private $turnier;

    #[ORM\Column(name: "turnier_id", type: "integer")] #[ORM\Id] private int $turnierId;

    #[ORM\Column(name: "bericht", type: "string", length: 1900, nullable: false)] private $bericht;

    #[ORM\Column(name: "kader_ueberprueft", type: "string", length: 0, nullable: false)] private $kaderUeberprueft;

    public function getBericht(): ?string
    {
        return $this->bericht;
    }

    public function setBericht(string $bericht): self
    {
        $this->bericht = $bericht;

        return $this;
    }

    public function getKaderUeberprueft(): ?string
    {
        return $this->kaderUeberprueft;
    }

    public function setKaderUeberprueft(string $kaderUeberprueft): self
    {
        $this->kaderUeberprueft = $kaderUeberprueft;

        return $this;
    }

    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    public function setTurnier(Turnier $turnier): void
    {
        $this->turnier = $turnier;
    }


}
