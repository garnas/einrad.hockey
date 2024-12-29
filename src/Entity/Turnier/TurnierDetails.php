<?php

namespace App\Entity\Turnier;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Table(name: "turniere_details")] #[ORM\Entity] class TurnierDetails
{
    #[ORM\Id]
    #[ORM\OneToOne(mappedBy: "turnier", targetEntity: Turnier::class)]
    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id", nullable: false)]
    private Turnier $turnier;

    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    public function setTurnier(Turnier $turnier): TurnierDetails
    {
        $this->turnier = $turnier;
        return $this;
    }

    #[ORM\Column(name: "hallenname", type: "string", length: 255, nullable: true)] private $hallenname;

    #[ORM\Column(name: "strasse", type: "string", length: 255, nullable: true)] private $strasse;

    #[ORM\Column(name: "plz", type: "string", length: 6)] private $plz;

    #[ORM\Column(name: "ort", type: "string", length: 255, nullable: true)] private $ort;

    #[ORM\Column(name: "haltestellen", type: "string", length: 255, nullable: true)] private $haltestellen;

    #[ORM\Column(name: "plaetze", type: "integer", nullable: true)] private $plaetze;

    #[ORM\Column(name: "min_teams", type: "integer", nullable: true)] private ?int $minTeams;

    #[ORM\Column(name: "format", type: "string", length: 255, nullable: true)] private $format;

    #[ORM\Column(name: "startzeit", type: "time")] private $startzeit;

    #[ORM\Column(name: "besprechung", type: "string", length: 0, nullable: true)] private $besprechung;

    #[ORM\Column(name: "hinweis", type: "string", length: 1700, nullable: true)] private $hinweis;

    #[ORM\Column(name: "organisator", type: "string", length: 255, nullable: true)] private $organisator;

    #[ORM\Column(name: "handy", type: "string", length: 255, nullable: true)] private $handy;

    #[ORM\Column(name: "startgebuehr", type: "string", length: 255, nullable: true)] private $startgebuehr;

    public function getTurnierId(): int
    {
        return $this->turnierId;
    }

    public function getHallenname(): ?string
    {
        return $this->hallenname;
    }

    public function setHallenname(?string $hallenname): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Hallenname", $this->hallenname ?? null, $hallenname);
        $this->hallenname = $hallenname;
        return $this;
    }

    public function getStrasse(): ?string
    {
        return $this->strasse;
    }

    public function setStrasse(?string $strasse): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Straße", $this->strasse ?? null, $strasse);
        $this->strasse = $strasse;
        return $this;
    }

    public function getPlz(): ?string
    {
        return $this->plz;
    }

    public function setPlz(?string $plz): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Plz", $this->plz ?? null, $plz);
        $this->plz = $plz;
        return $this;
    }

    public function getOrt(): ?string
    {
        return $this->ort;
    }

    public function setOrt(?string $ort): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Ort", $this->ort ?? null, $ort);
        $this->ort = $ort;
        return $this;
    }

    public function getHaltestellen(): ?string
    {
        return $this->haltestellen;
    }

    public function setHaltestellen(?string $haltestellen): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Haltestelle", $this->haltestellen ?? null, $haltestellen);
        $this->haltestellen = $haltestellen;
        return $this;
    }

    public function getPlaetze(): int
    {
        return $this->plaetze;
    }

    public function setPlaetze(int $plaetze): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Plätze", $this->plaetze ?? null, $plaetze);
        $this->plaetze = $plaetze;
        return $this;
    }

    public function getFormat(): ?string
    {
        return $this->format;
    }

    public function setFormat(?string $format): TurnierDetails
    {
        $this->format = $format;
        return $this;
    }

    public function getStartzeit(): DateTime
    {
        return $this->startzeit;
    }

    public function setStartzeit(DateTime $startzeit): TurnierDetails
    {
        $startzeitText = (isset($this->startzeit)) ? $this->startzeit->format("h:i") : null;
        $this->turnier->getLogService()->autoLog("Startzeit", $startzeitText, $startzeit->format("h:i"));
        $this->startzeit = $startzeit;
        return $this;
    }

    public function getBesprechung(): ?string
    {
        return $this->besprechung;
    }

    public function setBesprechung(?string $besprechung): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Besprechung", $this->besprechung ?? null, $besprechung);
        $this->besprechung = $besprechung;
        return $this;
    }

    public function getHinweis(): ?string
    {
        return $this->hinweis;
    }

    public function setHinweis(?string $hinweis): TurnierDetails
    {
        $hinweisText = isset($this->hinweis) ? "\r\n" . $this->hinweis : null;
        $this->turnier->getLogService()->autoLog("Hinweis", $hinweisText, "\r\n" . $hinweis);
        $this->hinweis = $hinweis;
        return $this;
    }

    public function getOrganisator(): ?string
    {
        return $this->organisator;
    }

    public function setOrganisator(?string $organisator): TurnierDetails
    {
        $this->turnier->getLogService()->autoLog("Organisator", $this->organisator ?? null, $organisator);
        $this->organisator = $organisator;
        return $this;
    }

    public function getHandy(): ?string
    {
        return $this->handy;
    }

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

    public function getStartgebuehr(): ?string
    {
        return $this->startgebuehr;
    }

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
