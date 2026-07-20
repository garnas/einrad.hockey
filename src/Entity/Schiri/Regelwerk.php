<?php

namespace App\Entity\Schiri;

use Doctrine\ORM\Mapping as ORM;

/**
 * schema.sql declares no primary key for this table; regelnummer is used as the natural
 * unique key throughout the legacy code (classes/schiritest.class.php).
 */
#[ORM\Entity]
#[ORM\Table(name: "regelwerk")]
class Regelwerk
{
    #[ORM\Id]
    #[ORM\GeneratedValue(strategy: "NONE")]
    #[ORM\Column(name: "regelnummer", type: "string", length: 10, nullable: false)]
    private string $regelnummer;

    #[ORM\Column(name: "regeltitel", type: "string", length: 100, nullable: true)]
    private ?string $regeltitel;

    #[ORM\Column(name: "regeltext", type: "string", length: 3000, nullable: true)]
    private ?string $regeltext;

    public function getRegelnummer(): string
    {
        return $this->regelnummer;
    }

    public function setRegelnummer(string $regelnummer): self
    {
        $this->regelnummer = $regelnummer;
        return $this;
    }

    public function getRegeltitel(): ?string
    {
        return $this->regeltitel;
    }

    public function setRegeltitel(?string $regeltitel): self
    {
        $this->regeltitel = $regeltitel;
        return $this;
    }

    public function getRegeltext(): ?string
    {
        return $this->regeltext;
    }

    public function setRegeltext(?string $regeltext): self
    {
        $this->regeltext = $regeltext;
        return $this;
    }

}
