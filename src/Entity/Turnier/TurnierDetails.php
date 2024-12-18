<?php

namespace App\Entity\Turnier;

use DateTime;
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
     * @var Turnier
     * @ORM\Id
     * @ORM\OneToOne(targetEntity="App\Entity\Turnier\Turnier", mappedBy="details")
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private $turnier;

    /**
     * @return Turnier
     */
    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    /**
     * @param Turnier $turnier
     * @return TurnierDetails
     */
    public function setTurnier(Turnier $turnier): TurnierDetails
    {
        $this->turnier = $turnier;
        return $this;
    }

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
     * @var string
     *
     * @ORM\Column(name="plz", type="string", length=6)
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
     * @var int|null
     *
     * @ORM\Column(name="min_teams", type="integer", nullable=true)
     */
    private ?int $minTeams;

    /**
     * @var string|null
     *
     * @ORM\Column(name="format", type="string", length=255, nullable=true)
     */
    private $format;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="startzeit", type="time")
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
        $this->turnier->getLogService()->autoLog("Hallenname", $this->hallenname ?? null, $hallenname);
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
        $this->turnier->getLogService()->autoLog("Straße", $this->strasse ?? null, $strasse);
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
        $this->turnier->getLogService()->autoLog("Plz", $this->plz ?? null, $plz);
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
        $this->turnier->getLogService()->autoLog("Ort", $this->ort ?? null, $ort);
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
        $this->turnier->getLogService()->autoLog("Haltestelle", $this->haltestellen ?? null, $haltestellen);
        $this->haltestellen = $haltestellen;
        return $this;
    }

    /**
     * @return int
     */
    public function getPlaetze(): int
    {
        return $this->plaetze;
    }

    /**
     * @param int $plaetze
     * @return TurnierDetails
     */
    public function setPlaetze(int $plaetze): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Plätze", $this->plaetze ?? null, $plaetze);
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
     * @return DateTime
     */
    public function getStartzeit(): DateTime
    {
        return $this->startzeit;
    }

    /**
     * @param DateTime $startzeit
     * @return TurnierDetails
     */
    public function setStartzeit(DateTime $startzeit): TurnierDetails
    {
        $startzeitText = (isset($this->startzeit)) ? $this->startzeit->format("h:i") : null;
        $this->turnier->getLogService()->autoLog("Startzeit", $startzeitText, $startzeit->format("h:i"));
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
        $this->turnier->getLogService()->autoLog("Besprechung", $this->besprechung ?? null, $besprechung);
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
        $hinweisText = isset($this->hinweis) ? "\r\n" . $this->hinweis : null;
        $this->turnier->getLogService()->autoLog("Hinweis", $hinweisText, "\r\n" . $hinweis);
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
        $this->turnier->getLogService()->autoLog("Organisator", $this->organisator ?? null, $organisator);
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
        $this->turnier->getLogService()->autoLog("Handy", $this->handy ?? null, $handy);
        $this->handy = $handy;
        return $this;
    }

    public function getBesprechungUhrzeit(): string
    {
        return $this->getStartzeit()->modify("- 15 minutes")->format("H:i");
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
        $this->turnier->getLogService()->autoLog("Startgebühr", $this->startgebuehr ?? null, $startgebuehr);
        $this->startgebuehr = $startgebuehr;
        return $this;
    }

    public function setMinTeams(?int $minTeams): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Minmale Anzahl an Teams", $this->minTeams ?? null, $minTeams);
        $this->minTeams = $minTeams;
        return $this;
    }

    public function getMinTeams(): ?int
    {
        return $this->minTeams;
    }

}
