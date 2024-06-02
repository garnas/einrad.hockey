<?php

namespace App\Entity\Turnier;

use Doctrine\ORM\Mapping as ORM;
use App\Entity\Turnier\Turnier;

/**
 * TurniereBerichte
 *
 */
#[ORM\Entity]
#[ORM\Table(name: "turniere_berichte", indexes: [new ORM\Index(name: "turnier_id", columns: ["turnier_id"])])]
class TurnierBericht
{

    /**
     * @var int
     */
    #[ORM\Column(name: "turnier_id", type: "integer")]
    #[ORM\Id] private int $turnierId;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: "bericht", type: "string", length: 1900, nullable: false)]
    private $bericht;

    /**
     * @var string
     *
     */
    #[ORM\Column(name: "kader_ueberprueft", type: "string", length: 0, nullable: false)]
    private $kaderUeberprueft;

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

}
