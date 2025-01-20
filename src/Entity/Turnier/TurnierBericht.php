<?php

namespace App\Entity\Turnier;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(
    name: "turniere_berichte",
    indexes: [
        new ORM\Index(name: "turnier_id", columns: ["turnier_id"])
    ])
]
class TurnierBericht
{

    #[ORM\OneToOne(targetEntity: Turnier::class)]
    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    private Turnier $turnier;

    #[ORM\Column(name: "turnier_id", type: "integer")]
    private int $turnierId;

    #[ORM\Column(name: "bericht_id", type: "integer")]
    #[ORM\Id]
    private int $berichtId;

    #[ORM\Column(name: "bericht", type: "string", length: 1900, nullable: false)]
    private string $bericht;

    #[ORM\Column(name: "kader_ueberprueft", type: "string", length: 0, nullable: false)]
    private string $kaderUeberprueft;

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
