<?php

namespace App\Entity\Turnier;

use Doctrine\ORM\Mapping as ORM;

/**
 * TurniereDetails
 *
 * @ORM\Table(name="turniere_details")
 * @ORM\Entity
 */
class TurnierDetails
{

    /**
     * @var int
     * @ORM\Id
     * @ORM\Column(name="turnier_id", type="integer")
     */
    private $turnierId;

    /**
     * @var string|null
     *
     * @ORM\Column(name="hallenname", type="string", length=255, nullable=true)
     */
    private $hallenname;

    /**
     * @var string|null
     *
     * @ORM\Column(name="strasse", type="string", length=255, nullable=true)
     */
    private $strasse;

    /**
     * @var string|null
     *
     * @ORM\Column(name="plz", type="string", length=6, nullable=true)
     */
    private $plz;

    /**
     * @var string|null
     *
     * @ORM\Column(name="ort", type="string", length=255, nullable=true)
     */
    private $ort;

    /**
     * @var string|null
     *
     * @ORM\Column(name="haltestellen", type="string", length=255, nullable=true)
     */
    private $haltestellen;

    /**
     * @var int|null
     *
     * @ORM\Column(name="plaetze", type="integer", nullable=true)
     */
    private $plaetze;

    /**
     * @var string|null
     *
     * @ORM\Column(name="format", type="string", length=255, nullable=true)
     */
    private $format;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="startzeit", type="time", nullable=true)
     */
    private $startzeit;

    /**
     * @var string|null
     *
     * @ORM\Column(name="besprechung", type="string", length=0, nullable=true)
     */
    private $besprechung;

    /**
     * @var string|null
     *
     * @ORM\Column(name="hinweis", type="string", length=1700, nullable=true)
     */
    private $hinweis;

    /**
     * @var string|null
     *
     * @ORM\Column(name="organisator", type="string", length=255, nullable=true)
     */
    private $organisator;

    /**
     * @var string|null
     *
     * @ORM\Column(name="handy", type="string", length=255, nullable=true)
     */
    private $handy;

    /**
     * @var string|null
     *
     * @ORM\Column(name="startgebuehr", type="string", length=255, nullable=true)
     */
    private $startgebuehr;

    /**
     * @return int
     */
    public function getTurnierId(): int
    {
        return $this->turnierId;
    }

    /**
     * @param int $turnierId
     * @return TurnierDetails
     */
    public function setTurnierId(int $turnierId): TurnierDetails
    {
        $this->turnierId = $turnierId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHallenname(): ?string
    {
        return $this->hallenname;
    }

    /**
     * @param string|null $hallenname
     * @return TurnierDetails
     */
    public function setHallenname(?string $hallenname): TurnierDetails
    {
        $this->hallenname = $hallenname;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStrasse(): ?string
    {
        return $this->strasse;
    }

    /**
     * @param string|null $strasse
     * @return TurnierDetails
     */
    public function setStrasse(?string $strasse): TurnierDetails
    {
        $this->strasse = $strasse;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPlz(): ?string
    {
        return $this->plz;
    }

    /**
     * @param string|null $plz
     * @return TurnierDetails
     */
    public function setPlz(?string $plz): TurnierDetails
    {
        $this->plz = $plz;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrt(): ?string
    {
        return $this->ort;
    }

    /**
     * @param string|null $ort
     * @return TurnierDetails
     */
    public function setOrt(?string $ort): TurnierDetails
    {
        $this->ort = $ort;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHaltestellen(): ?string
    {
        return $this->haltestellen;
    }

    /**
     * @param string|null $haltestellen
     * @return TurnierDetails
     */
    public function setHaltestellen(?string $haltestellen): TurnierDetails
    {
        $this->haltestellen = $haltestellen;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getPlaetze(): ?int
    {
        return $this->plaetze;
    }

    /**
     * @param int|null $plaetze
     * @return TurnierDetails
     */
    public function setPlaetze(?int $plaetze): TurnierDetails
    {
        $this->plaetze = $plaetze;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFormat(): ?string
    {
        return $this->format;
    }

    /**
     * @param string|null $format
     * @return TurnierDetails
     */
    public function setFormat(?string $format): TurnierDetails
    {
        $this->format = $format;
        return $this;
    }

    /**
     * @return \DateTime|null
     */
    public function getStartzeit(): ?\DateTime
    {
        return $this->startzeit;
    }

    /**
     * @param \DateTime|null $startzeit
     * @return TurnierDetails
     */
    public function setStartzeit(?\DateTime $startzeit): TurnierDetails
    {
        $this->startzeit = $startzeit;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBesprechung(): ?string
    {
        return $this->besprechung;
    }

    /**
     * @param string|null $besprechung
     * @return TurnierDetails
     */
    public function setBesprechung(?string $besprechung): TurnierDetails
    {
        $this->besprechung = $besprechung;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHinweis(): ?string
    {
        return $this->hinweis;
    }

    /**
     * @param string|null $hinweis
     * @return TurnierDetails
     */
    public function setHinweis(?string $hinweis): TurnierDetails
    {
        $this->hinweis = $hinweis;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getOrganisator(): ?string
    {
        return $this->organisator;
    }

    /**
     * @param string|null $organisator
     * @return TurnierDetails
     */
    public function setOrganisator(?string $organisator): TurnierDetails
    {
        $this->organisator = $organisator;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getHandy(): ?string
    {
        return $this->handy;
    }

    /**
     * @param string|null $handy
     * @return TurnierDetails
     */
    public function setHandy(?string $handy): TurnierDetails
    {
        $this->handy = $handy;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getStartgebuehr(): ?string
    {
        return $this->startgebuehr;
    }

    /**
     * @param string|null $startgebuehr
     * @return TurnierDetails
     */
    public function setStartgebuehr(?string $startgebuehr): TurnierDetails
    {
        $this->startgebuehr = $startgebuehr;
        return $this;
    }

}
