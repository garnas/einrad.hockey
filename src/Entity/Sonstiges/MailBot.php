<?php

namespace App\Entity\Sonstiges;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * Mailbot
 *
 * @ORM\Table(name="mailbot")
 * @ORM\Entity
 */
class Mailbot
{
    /**
     * @var int
     *
     * @ORM\Column(name="mail_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $mailId;

    /**
     * @var string
     *
     * @ORM\Column(name="adressat", type="string", length=600, nullable=false)
     */
    private $adressat;

    /**
     * @var string
     *
     * @ORM\Column(name="betreff", type="string", length=255, nullable=false)
     */
    private $betreff;

    /**
     * @var string
     *
     * @ORM\Column(name="absender", type="string", length=255, nullable=false)
     */
    private $absender;

    /**
     * @var string
     *
     * @ORM\Column(name="inhalt", type="string", length=2000, nullable=false)
     */
    private $inhalt;

    /**
     * @var string
     *
     * @ORM\Column(name="mail_status", type="string", length=0, nullable=false)
     */
    private $mailStatus;

    /**
     * @var string|null
     *
     * @ORM\Column(name="fehler", type="string", length=300, nullable=true)
     */
    private $fehler = '';

    /**
     * @var DateTime
     *
     * @ORM\Column(name="zeit", type="datetime", nullable=false, options={"default"="CURRENT_TIMESTAMP"})
     */
    private DateTime $zeit;

    public function getMailId(): ?int
    {
        return $this->mailId;
    }

    public function getAdressat(): ?string
    {
        return $this->adressat;
    }

    public function setAdressat(string $adressat): self
    {
        $this->adressat = $adressat;

        return $this;
    }

    public function getBetreff(): ?string
    {
        return $this->betreff;
    }

    public function setBetreff(string $betreff): self
    {
        $this->betreff = $betreff;

        return $this;
    }

    public function getAbsender(): ?string
    {
        return $this->absender;
    }

    public function setAbsender(string $absender): self
    {
        $this->absender = $absender;

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

    public function getMailStatus(): ?string
    {
        return $this->mailStatus;
    }

    public function setMailStatus(string $mailStatus): self
    {
        $this->mailStatus = $mailStatus;

        return $this;
    }

    public function getFehler(): ?string
    {
        return $this->fehler;
    }

    public function setFehler(?string $fehler): self
    {
        $this->fehler = $fehler;

        return $this;
    }

    public function getZeit(): DateTime
    {
        return $this->zeit;
    }

    public function setZeit(DateTime $zeit): self
    {
        $this->zeit = $zeit;

        return $this;
    }

}
