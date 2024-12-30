<?php

namespace App\Entity\Team;

use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "teams_details", uniqueConstraints: [new ORM\UniqueConstraint(name: "team_id", columns: ["team_id"])])]
class TeamDetails
{

    #[ORM\Id]
    #[ORM\Column(name: "team_id", type: "integer")]
    private int $team_id;

    #[ORM\Column(name: "plz", type: "string", length: 255, nullable: true)]
    private ?string $plz;

    #[ORM\Column(name: "ort", type: "string", length: 255, nullable: true)]
    private ?string $ort;

    #[ORM\Column(name: "verein", type: "string", length: 255, nullable: true)]
    private ?string $verein;

    #[ORM\Column(name: "homepage", type: "string", length: 255, nullable: true)]
    private ?string $homepage;

    #[ORM\Column(name: "ligavertreter", type: "string", length: 255, nullable: true)]
    private ?string $ligavertreter;

    #[ORM\Column(name: "teamfoto", type: "string", length: 255, nullable: true)]
    private ?string $teamfoto;

    #[ORM\Column(name: "trikot_farbe_1", type: "string", length: 9, nullable: true)]
    private ?string $trikotFarbe1;

    #[ORM\Column(name: "trikot_farbe_2", type: "string", length: 9, nullable: true)]
    private ?string $trikotFarbe2;

    public function getPlz(): ?string
    {
        return $this->plz;
    }

    public function setPlz(?string $plz): self
    {
        $this->plz = $plz;

        return $this;
    }

    public function getOrt(): ?string
    {
        return $this->ort;
    }

    public function setOrt(?string $ort): self
    {
        $this->ort = $ort;

        return $this;
    }

    public function getVerein(): ?string
    {
        return $this->verein;
    }

    public function setVerein(?string $verein): self
    {
        $this->verein = $verein;

        return $this;
    }

    public function getHomepage(): ?string
    {
        return $this->homepage;
    }

    public function setHomepage(?string $homepage): self
    {
        $this->homepage = $homepage;

        return $this;
    }

    public function getLigavertreter(): ?string
    {
        return $this->ligavertreter;
    }

    public function setLigavertreter(?string $ligavertreter): self
    {
        $this->ligavertreter = $ligavertreter;

        return $this;
    }

    public function getTeamfoto(): ?string
    {
        return $this->teamfoto;
    }

    public function setTeamfoto(?string $teamfoto): self
    {
        $this->teamfoto = $teamfoto;

        return $this;
    }

    public function getTrikotFarbe1(): ?string
    {
        return $this->trikotFarbe1;
    }

    public function setTrikotFarbe1(?string $trikotFarbe1): self
    {
        $this->trikotFarbe1 = $trikotFarbe1;

        return $this;
    }

    public function getTrikotFarbe2(): ?string
    {
        return $this->trikotFarbe2;
    }

    public function setTrikotFarbe2(?string $trikotFarbe2): self
    {
        $this->trikotFarbe2 = $trikotFarbe2;

        return $this;
    }

    public function get_team_id(): ?string
    {
        return $this->team_id;
    }

}
