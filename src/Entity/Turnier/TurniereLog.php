<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * TurniereLog
 *
 * @ORM\Table(name="turniere_log", indexes={@ORM\Index(name="turnier_id", columns={"turnier_id"})})
 * @ORM\Entity
 */
class TurniereLog
{
    /**
     * @var int
     *
     * @ORM\Column(name="turnier_log_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $turnierLogId;

    /**
     * @var int
     *
     * @ORM\Column(name="turnier_id", type="integer", nullable=false)
     */
    private $turnierId;

    /**
     * @var string
     *
     * @ORM\Column(name="log_text", type="string", length=9000, nullable=false)
     */
    private $logText;

    /**
     * @var string
     *
     * @ORM\Column(name="autor", type="string", length=255, nullable=false)
     */
    private $autor;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="zeit", type="datetime", nullable=false, options={"default"="current_timestamp(1)"})
     */
    private $zeit = 'current_timestamp(1)';

    public function getTurnierLogId(): ?int
    {
        return $this->turnierLogId;
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
