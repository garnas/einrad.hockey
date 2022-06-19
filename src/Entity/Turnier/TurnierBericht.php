<?php

namespace App\Entity\Turnier;

use Doctrine\ORM\Mapping as ORM;

/**
 * TurniereBerichte
 *
 * @ORM\Table(name="turniere_berichte", indexes={@ORM\Index(name="turnier_id", columns={"turnier_id"})})
 * @ORM\Entity
 */
class TurnierBericht
{

    /**
     * @var Turnier
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\Entity\Turnier\Turnier", mappedBy="turnier")
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private $turnier;

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="turnier_id", type="integer")
     */
    private int $turnierId;

    /**
     * @var string
     *
     * @ORM\Column(name="bericht", type="string", length=1900, nullable=false)
     */
    private $bericht;

    /**
     * @var string
     *
     * @ORM\Column(name="kader_ueberprueft", type="string", length=0, nullable=false)
     */
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

    /**
     * @return Turnier
     */
    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    /**
     * @param Turnier $turnier
     */
    public function setTurnier(Turnier $turnier): void
    {
        $this->turnier = $turnier;
    }


}
