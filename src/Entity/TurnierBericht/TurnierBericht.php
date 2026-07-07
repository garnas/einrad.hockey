<?php

namespace App\Entity\TurnierBericht;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Turnier\Turnier;

#[ORM\Entity]
#[ORM\Table(
    name: "turniere_berichte",
    indexes: [
        new ORM\Index(name: "turnier_id", columns: ["turnier_id"]),
    ],
)]
class TurnierBericht
{
    #[ORM\Id]
    #[ORM\GeneratedValue('AUTO')]
    #[ORM\Column(name: "bericht_id", type: "integer")]
    private int $berichtId;

    #[ORM\OneToOne(targetEntity: Turnier::class)]
    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    private Turnier $turnier;

    #[ORM\Column(name: "bericht", type: "string", length: 1900, nullable: false)]
    private string $bericht = '';

    #[ORM\Column(name: "kader_ueberprueft", type: "string", length: 10, nullable: false)]
    private string $kaderUeberprueft = 'Nein';

    public function __construct(Turnier $turnier)
    {
        $this->turnier = $turnier;
    }

    public function getId(): int
    {
        return $this->berichtId;
    }
        
    public function getBericht(): string
    {
        return $this->bericht;
    }

    public function setBericht(string $bericht): self
    {
        $this->bericht = $bericht;

        return $this;
    }

    public function getKaderUeberprueft(): string
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
