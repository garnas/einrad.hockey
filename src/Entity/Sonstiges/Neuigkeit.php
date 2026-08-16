<?php

namespace App\Entity\Sonstiges;

use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;
use App\Enum\NeuigkeitArt;

#[ORM\Table(name: "neuigkeiten")]
#[ORM\Entity] class Neuigkeit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(name: "neuigkeiten_id", type: "integer")]
    private ?int $neuigkeitenId = null;

    #[ORM\Column(name: "titel", type: "string", length: 255, nullable: true)]
    private ?string $titel = null;

    #[ORM\Column(name: "inhalt", type: "string", length: 1800)]
    private string $inhalt;

    #[ORM\Column(name: "link_pdf", type: "string", length: 255)]
    private string $linkPdf;

    #[ORM\Column(name: "link_jpg", type: "string", length: 255)]
    private string $linkJpg;

    #[ORM\Column(name: "bild_verlinken", type: "string", length: 255)]
    private string $bildVerlinken;

    #[ORM\Column(name: "eingetragen_von", type: "string", length: 255)]
    private string $eingetragenVon;

    #[ORM\Column(name: "aktiv", type: "boolean", options: ["default" => 1])]
    private bool $aktiv = true;

    #[ORM\Column(name: "art", type: "string", enumType: NeuigkeitArt::class)]
    private NeuigkeitArt $art = NeuigkeitArt::NEUIGKEIT;

    #[ORM\Column(name: "zeit", type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private DateTimeInterface $zeit;

    public function getNeuigkeitenId(): ?int
    {
        return $this->neuigkeitenId;
    }

    public function getTitel(): ?string
    {
        return $this->titel;
    }

    public function setTitel(?string $titel): self
    {
        $this->titel = $titel;
        return $this;
    }

    public function getInhalt(): ?string
    {
        return $this->inhalt;
    }

    public function setInhalt(string $inhalt): self
    {
        $this->inhalt = $inhalt;
        return $this;
    }

    public function getLinkPdf(): ?string
    {
        return $this->linkPdf;
    }

    public function setLinkPdf(string $linkPdf): self
    {
        $this->linkPdf = $linkPdf;
        return $this;
    }

    public function getLinkJpg(): ?string
    {
        return $this->linkJpg;
    }

    public function setLinkJpg(string $linkJpg): self
    {
        $this->linkJpg = $linkJpg;
        return $this;
    }

    public function getBildVerlinken(): ?string
    {
        return $this->bildVerlinken;
    }

    public function setBildVerlinken(string $bildVerlinken): self
    {
        $this->bildVerlinken = $bildVerlinken;
        return $this;
    }

    public function getEingetragenVon(): ?string
    {
        return $this->eingetragenVon;
    }

    public function setEingetragenVon(string $eingetragenVon): self
    {
        $this->eingetragenVon = $eingetragenVon;
        return $this;
    }

    public function isAktiv(): bool
    {
        return $this->aktiv;
    }

    public function setAktiv(bool $aktiv): self
    {
        $this->aktiv = $aktiv;
        return $this;
    }

    public function getArt(): NeuigkeitArt
    {
        return $this->art;
    }

    public function setArt(NeuigkeitArt $art): self
    {
        $this->art = $art;
        return $this;
    }

    public function getZeit(): ?DateTimeInterface
    {
        return $this->zeit;
    }

    public function setZeit(DateTimeInterface $zeit): self
    {
        $this->zeit = $zeit;
        return $this;
    }
}
