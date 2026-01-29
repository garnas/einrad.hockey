<?php

namespace App\Entity\Sonstiges;

use Doctrine\ORM\Mapping as ORM;
use App\Enum\NeuigkeitArt;

#[ORM\Table(name: "neuigkeiten")]
#[ORM\Entity] class Neuigkeit
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: "integer")]
    private ?int $neuigkeiten_id = null;

    #[ORM\Column(type: "string", length: 255, nullable: true)]
    private ?string $titel = null;

    #[ORM\Column(type: "string", length: 1800)]
    private string $inhalt;

    #[ORM\Column(type: "string", length: 255)]
    private string $link_pdf;

    #[ORM\Column(type: "string", length: 255)]
    private string $link_jpg;

    #[ORM\Column(type: "string", length: 255)]
    private string $bild_verlinken;

    #[ORM\Column(type: "string", length: 255)]
    private string $eingetragen_von;

    #[ORM\Column(type: "boolean", options: ["default" => 1])]
    private bool $aktiv = true;

    #[ORM\Column(type: "string", enumType: NeuigkeitArt::class)]
    private NeuigkeitArt $art = NeuigkeitArt::NEUIGKEIT;

    #[ORM\Column(type: "datetime", options: ["default" => "CURRENT_TIMESTAMP"])]
    private \DateTimeInterface $zeit;

    public function getNeuigkeitenId(): ?int
    {
        return $this->neuigkeiten_id;
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
        return $this->link_pdf;
    }

    public function setLinkPdf(string $link_pdf): self
    {
        $this->link_pdf = $link_pdf;
        return $this;
    }

    public function getLinkJpg(): ?string
    {
        return $this->link_jpg;
    }

    public function setLinkJpg(string $link_jpg): self
    {
        $this->link_jpg = $link_jpg;
        return $this;
    }

    public function getBildVerlinken(): ?string
    {
        return $this->bild_verlinken;
    }

    public function setBildVerlinken(string $bild_verlinken): self
    {
        $this->bild_verlinken = $bild_verlinken;
        return $this;
    }

    public function getEingetragenVon(): ?string
    {
        return $this->eingetragen_von;
    }

    public function setEingetragenVon(string $eingetragen_von): self
    {
        $this->eingetragen_von = $eingetragen_von;
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

    public function getZeit(): ?\DateTimeInterface
    {
        return $this->zeit;
    }

    public function setZeit(\DateTimeInterface $zeit): self
    {
        $this->zeit = $zeit;
        return $this;
    }
}
