<?php

namespace App\Entity\Turnier;

use DateTime;
use DateTimeInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * TurniereLog
 *
 */
#[ORM\Entity] #[ORM\Table(name: "turniere_log", indexes: [new ORM\Index(columns: ["turnier_id"], name: "turnier_id")])] class TurniereLog
{

    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "turnier_log_id", type: "integer", nullable: false)]
    private int $id;

    #[ORM\Column(name: "turnier_id", type: "integer", nullable: false)] private $turnierId;

    #[ORM\Column(name: "log_text", type: "string", length: 9000, nullable: false)] private $logText;

    #[ORM\Column(name: "autor", type: "string", length: 255, nullable: false)] private $autor;

    #[ORM\Column(name: "zeit", type: "datetime", nullable: false, options: ["default" => "current_timestamp(1)"])] private DateTime $zeit;

    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    #[ORM\ManyToOne(targetEntity: "App\Entity\Turnier\Turnier", inversedBy: "turnier")] private Turnier $turnier;

    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    public function setTurnier(Turnier $turnier): TurniereLog
    {
        $this->turnier = $turnier;
        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTurnierId(): ?int
    {
        return $this->turnierId;
    }

    public function setTurnierId(int $turnierId): self
    {
        $this->turnierId = $turnierId;

        return $this;
    }

    public function getLogText(): ?string
    {
        return $this->logText;
    }

    public function setLogText(string $logText): self
    {
        $this->logText = $logText;

        return $this;
    }

    public function getAutor(): ?string
    {
        return $this->autor;
    }

    public function setAutor(string $autor): self
    {
        $this->autor = $autor;

        return $this;
    }

    public function getZeit(): ?DateTimeInterface
    {
        return $this->zeit;
    }

}
