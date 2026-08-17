<?php

namespace App\Entity\Schiri;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "schiri_ergebnis")]
class SchiriErgebnis
{
    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "schiri_test_id", type: "integer", nullable: false)]
    private int $schiriTestId;

    #[ORM\Column(name: "md5sum", type: "string", length: 32, nullable: true)]
    private ?string $md5sum;

    #[ORM\Column(name: "spieler_id", type: "integer", nullable: false)]
    private int $spielerId;

    #[ORM\Column(name: "spieler_email", type: "string", length: 500, nullable: true)]
    private ?string $spielerEmail;

    #[ORM\Column(name: "gestellte_fragen", type: "string", length: 500, nullable: true)]
    private ?string $gestellteFragen;

    #[ORM\Column(name: "gesetzte_antworten", type: "string", length: 500, nullable: true)]
    private ?string $gesetzteAntworten;

    #[ORM\Column(name: "test_level", type: "string", length: 0, nullable: true)]
    private ?string $testLevel;

    #[ORM\Column(name: "bestanden", type: "string", length: 0, nullable: true)]
    private ?string $bestanden;

    #[ORM\Column(name: "kommentar", type: "string", length: 500, nullable: true)]
    private ?string $kommentar;

    #[ORM\Column(name: "t_erstellt", type: "datetime", nullable: true)]
    private ?DateTime $tErstellt;

    #[ORM\Column(name: "t_gestartet", type: "datetime", nullable: true)]
    private ?DateTime $tGestartet;

    #[ORM\Column(name: "t_abgegeben", type: "datetime", nullable: true)]
    private ?DateTime $tAbgegeben;

    #[ORM\Column(name: "saison", type: "integer", nullable: false)]
    private int $saison;

    #[ORM\Column(name: "schiri_test_version", type: "integer", nullable: false)]
    private int $schiriTestVersion;

    public function getSchiriTestId(): ?int
    {
        return $this->schiriTestId;
    }

    public function getMd5sum(): ?string
    {
        return $this->md5sum;
    }

    public function setMd5sum(?string $md5sum): self
    {
        $this->md5sum = $md5sum;
        return $this;
    }

    public function getSpielerId(): int
    {
        return $this->spielerId;
    }

    public function setSpielerId(int $spielerId): self
    {
        $this->spielerId = $spielerId;
        return $this;
    }

    public function getSpielerEmail(): ?string
    {
        return $this->spielerEmail;
    }

    public function setSpielerEmail(?string $spielerEmail): self
    {
        $this->spielerEmail = $spielerEmail;
        return $this;
    }

    public function getGestellteFragen(): ?string
    {
        return $this->gestellteFragen;
    }

    public function setGestellteFragen(?string $gestellteFragen): self
    {
        $this->gestellteFragen = $gestellteFragen;
        return $this;
    }

    public function getGesetzteAntworten(): ?string
    {
        return $this->gesetzteAntworten;
    }

    public function setGesetzteAntworten(?string $gesetzteAntworten): self
    {
        $this->gesetzteAntworten = $gesetzteAntworten;
        return $this;
    }

    public function getTestLevel(): ?string
    {
        return $this->testLevel;
    }

    public function setTestLevel(?string $testLevel): self
    {
        $this->testLevel = $testLevel;
        return $this;
    }

    public function getBestanden(): ?string
    {
        return $this->bestanden;
    }

    public function setBestanden(?string $bestanden): self
    {
        $this->bestanden = $bestanden;
        return $this;
    }

    public function isBestanden(): bool
    {
        return $this->bestanden === "Ja";
    }

    public function getKommentar(): ?string
    {
        return $this->kommentar;
    }

    public function setKommentar(?string $kommentar): self
    {
        $this->kommentar = $kommentar;
        return $this;
    }

    public function getTErstellt(): ?DateTime
    {
        return $this->tErstellt;
    }

    public function setTErstellt(?DateTime $tErstellt): self
    {
        $this->tErstellt = $tErstellt;
        return $this;
    }

    public function getTGestartet(): ?DateTime
    {
        return $this->tGestartet;
    }

    public function setTGestartet(?DateTime $tGestartet): self
    {
        $this->tGestartet = $tGestartet;
        return $this;
    }

    public function getTAbgegeben(): ?DateTime
    {
        return $this->tAbgegeben;
    }

    public function setTAbgegeben(?DateTime $tAbgegeben): self
    {
        $this->tAbgegeben = $tAbgegeben;
        return $this;
    }

    public function getSaison(): int
    {
        return $this->saison;
    }

    public function setSaison(int $saison): self
    {
        $this->saison = $saison;
        return $this;
    }

    public function getSchiriTestVersion(): int
    {
        return $this->schiriTestVersion;
    }

    public function setSchiriTestVersion(int $schiriTestVersion): self
    {
        $this->schiriTestVersion = $schiriTestVersion;
        return $this;
    }

}
