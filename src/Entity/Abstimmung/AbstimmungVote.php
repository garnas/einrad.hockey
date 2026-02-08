<?php

namespace App\Entity\Abstimmung;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "abstimmung_stimmen")]
class AbstimmungVote
{
    #[ORM\Id]
    #[ORM\Column(type: 'string', length: 255)]
    private string $crypt;

    #[ORM\Column(type: 'string', length: 511)]
    private string $stimme;

    public function getCrypt(): string
    {
        return $this->crypt;
    }

    public function setCrypt(string $crypt): self
    {
        $this->crypt = $crypt;
        return $this;
    }

    public function getStimme(): string
    {
        return $this->stimme;
    }

    public function setStimme(string $stimme): self
    {
        $this->stimme = $stimme;
        return $this;
    }
}