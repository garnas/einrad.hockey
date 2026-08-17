<?php

namespace App\Entity\Schiri;

use Doctrine\ORM\Mapping as ORM;

/**
 * schema.sql declares no primary key for this table; frage_id is used as the natural
 * unique key throughout the legacy code (classes/schiritest.class.php).
 */
#[ORM\Entity]
#[ORM\Table(name: "schiri_test")]
class SchiriTest
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "frage_id", type: "integer", nullable: false)]
    private int $frageId;

    #[ORM\Column(name: "kategorie", type: "string", length: 6, nullable: true)]
    private ?string $kategorie;

    #[ORM\Column(name: "LJBF", type: "string", length: 4, nullable: true)]
    private ?string $ljbf;

    #[ORM\Column(name: "interne_notiz", type: "string", length: 591, nullable: true)]
    private ?string $interneNotiz;

    #[ORM\Column(name: "frage", type: "string", length: 541, nullable: true)]
    private ?string $frage;

    #[ORM\Column(name: "antwort_1", type: "string", length: 305, nullable: true)]
    private ?string $antwort1;

    #[ORM\Column(name: "antwort_2", type: "string", length: 259, nullable: true)]
    private ?string $antwort2;

    #[ORM\Column(name: "antwort_3", type: "string", length: 217, nullable: true)]
    private ?string $antwort3;

    #[ORM\Column(name: "antwort_4", type: "string", length: 347, nullable: true)]
    private ?string $antwort4;

    #[ORM\Column(name: "antwort_5", type: "string", length: 135, nullable: true)]
    private ?string $antwort5;

    #[ORM\Column(name: "antwort_6", type: "string", length: 111, nullable: true)]
    private ?string $antwort6;

    #[ORM\Column(name: "richtig", type: "string", length: 9, nullable: true)]
    private ?string $richtig;

    #[ORM\Column(name: "regelnr", type: "string", length: 14, nullable: true)]
    private ?string $regelnr;

    #[ORM\Column(name: "erklaerung", type: "string", length: 609, nullable: true)]
    private ?string $erklaerung;

    #[ORM\Column(name: "punkte", type: "integer", nullable: true)]
    private ?int $punkte;

    #[ORM\Column(name: "name_bild", type: "string", length: 14, nullable: true)]
    private ?string $nameBild;

    #[ORM\Column(name: "name_video", type: "string", length: 14, nullable: true)]
    private ?string $nameVideo;

    #[ORM\Column(name: "erklaerung_bild", type: "string", length: 16, nullable: true)]
    private ?string $erklaerungBild;

    #[ORM\Column(name: "erklaerung_video", type: "integer", nullable: true)]
    private ?int $erklaerungVideo;

    #[ORM\Column(name: "bestaetigt", type: "string", length: 4, nullable: true)]
    private ?string $bestaetigt;

    public function getFrageId(): int
    {
        return $this->frageId;
    }

    public function setFrageId(int $frageId): self
    {
        $this->frageId = $frageId;
        return $this;
    }

    public function getKategorie(): ?string
    {
        return $this->kategorie;
    }

    public function setKategorie(?string $kategorie): self
    {
        $this->kategorie = $kategorie;
        return $this;
    }

    public function getLjbf(): ?string
    {
        return $this->ljbf;
    }

    public function setLjbf(?string $ljbf): self
    {
        $this->ljbf = $ljbf;
        return $this;
    }

    public function getInterneNotiz(): ?string
    {
        return $this->interneNotiz;
    }

    public function setInterneNotiz(?string $interneNotiz): self
    {
        $this->interneNotiz = $interneNotiz;
        return $this;
    }

    public function getFrage(): ?string
    {
        return $this->frage;
    }

    public function setFrage(?string $frage): self
    {
        $this->frage = $frage;
        return $this;
    }

    public function getAntwort1(): ?string
    {
        return $this->antwort1;
    }

    public function setAntwort1(?string $antwort1): self
    {
        $this->antwort1 = $antwort1;
        return $this;
    }

    public function getAntwort2(): ?string
    {
        return $this->antwort2;
    }

    public function setAntwort2(?string $antwort2): self
    {
        $this->antwort2 = $antwort2;
        return $this;
    }

    public function getAntwort3(): ?string
    {
        return $this->antwort3;
    }

    public function setAntwort3(?string $antwort3): self
    {
        $this->antwort3 = $antwort3;
        return $this;
    }

    public function getAntwort4(): ?string
    {
        return $this->antwort4;
    }

    public function setAntwort4(?string $antwort4): self
    {
        $this->antwort4 = $antwort4;
        return $this;
    }

    public function getAntwort5(): ?string
    {
        return $this->antwort5;
    }

    public function setAntwort5(?string $antwort5): self
    {
        $this->antwort5 = $antwort5;
        return $this;
    }

    public function getAntwort6(): ?string
    {
        return $this->antwort6;
    }

    public function setAntwort6(?string $antwort6): self
    {
        $this->antwort6 = $antwort6;
        return $this;
    }

    public function getRichtig(): ?string
    {
        return $this->richtig;
    }

    public function setRichtig(?string $richtig): self
    {
        $this->richtig = $richtig;
        return $this;
    }

    public function getRegelnr(): ?string
    {
        return $this->regelnr;
    }

    public function setRegelnr(?string $regelnr): self
    {
        $this->regelnr = $regelnr;
        return $this;
    }

    public function getErklaerung(): ?string
    {
        return $this->erklaerung;
    }

    public function setErklaerung(?string $erklaerung): self
    {
        $this->erklaerung = $erklaerung;
        return $this;
    }

    public function getPunkte(): ?int
    {
        return $this->punkte;
    }

    public function setPunkte(?int $punkte): self
    {
        $this->punkte = $punkte;
        return $this;
    }

    public function getNameBild(): ?string
    {
        return $this->nameBild;
    }

    public function setNameBild(?string $nameBild): self
    {
        $this->nameBild = $nameBild;
        return $this;
    }

    public function getNameVideo(): ?string
    {
        return $this->nameVideo;
    }

    public function setNameVideo(?string $nameVideo): self
    {
        $this->nameVideo = $nameVideo;
        return $this;
    }

    public function getErklaerungBild(): ?string
    {
        return $this->erklaerungBild;
    }

    public function setErklaerungBild(?string $erklaerungBild): self
    {
        $this->erklaerungBild = $erklaerungBild;
        return $this;
    }

    public function getErklaerungVideo(): ?int
    {
        return $this->erklaerungVideo;
    }

    public function setErklaerungVideo(?int $erklaerungVideo): self
    {
        $this->erklaerungVideo = $erklaerungVideo;
        return $this;
    }

    public function getBestaetigt(): ?string
    {
        return $this->bestaetigt;
    }

    public function setBestaetigt(?string $bestaetigt): self
    {
        $this->bestaetigt = $bestaetigt;
        return $this;
    }

}
