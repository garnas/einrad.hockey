<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TurniereGeloescht
 *
 * @ORM\Table(name="turniere_geloescht")
 * @ORM\Entity
 */
class TurniereGeloescht
{
    /**
     * @var int
     *
     * @ORM\Column(name="turnier_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $turnierId;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="datum", type="date", nullable=false)
     */
    private $datum;

    /**
     * @var string
     *
     * @ORM\Column(name="ort", type="string", length=255, nullable=false)
     */
    private $ort;

    /**
     * @var string
     *
     * @ORM\Column(name="grund", type="string", length=255, nullable=false)
     */
    private $grund;

    /**
     * @var int
     *
     * @ORM\Column(name="saison", type="integer", nullable=false)
     */
    private $saison;

    public function getTurnierId(): ?int
    {
        return $this->turnierId;
    }

    public function getDatum(): ?\DateTimeInterface
    {
        return $this->datum;
    }

    public function setDatum(\DateTimeInterface $datum): self
    {
        $this->datum = $datum;

        return $this;
    }

    public function getOrt(): ?string
    {
        return $this->ort;
    }

    public function setOrt(string $ort): self
    {
        $this->ort = $ort;

        return $this;
    }

    public function getGrund(): ?string
    {
        return $this->grund;
    }

    public function setGrund(string $grund): self
    {
        $this->grund = $grund;

        return $this;
    }

    public function getSaison(): ?int
    {
        return $this->saison;
    }

    public function setSaison(int $saison): self
    {
        $this->saison = $saison;

        return $this;
    }


}
