<?php

namespace App\Entity\Turnier;

use DateTime;
use DateTimeInterface;
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
    private int $id;

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
     * @var DateTime
     *
     * @ORM\Column(name="zeit", type="datetime", nullable=false, options={"default"="current_timestamp(1)"})
     */
    private DateTime $zeit;

    /**
     * @var Turnier
     * @ORM\ManyToOne(targetEntity="App\Entity\Turnier\Turnier", inversedBy="turnier")
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private Turnier $turnier;

    /**
     * @return Turnier
     */
    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    /**
     * @param Turnier $turnier
     * @return TurniereLog
     */
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
