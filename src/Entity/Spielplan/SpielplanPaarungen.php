<?php

namespace App\Entity\Spielplan;

use Doctrine\ORM\Mapping as ORM;

/**
 * SpielplanPaarungen
 *
 * @ORM\Table(name="spielplan_paarungen")
 * @ORM\Entity
 */
class SpielplanPaarungen
{
    /**
     * @var string
     *
     * @ORM\Column(name="spielplan_paarung", type="string", length=30, nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $spielplanPaarung;

    /**
     * @var bool
     *
     * @ORM\Column(name="spiel_id", type="boolean", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="NONE")
     */
    private $spielId;

    /**
     * @var bool
     *
     * @ORM\Column(name="team_a", type="boolean", nullable=false)
     */
    private $teamA;

    /**
     * @var bool
     *
     * @ORM\Column(name="team_b", type="boolean", nullable=false)
     */
    private $teamB;

    /**
     * @var bool
     *
     * @ORM\Column(name="schiri_a", type="boolean", nullable=false)
     */
    private $schiriA;

    /**
     * @var bool
     *
     * @ORM\Column(name="schiri_b", type="boolean", nullable=false)
     */
    private $schiriB;

    public function getSpielplanPaarung(): ?string
    {
        return $this->spielplanPaarung;
    }

    public function isSpielId(): ?bool
    {
        return $this->spielId;
    }

    public function isTeamA(): ?bool
    {
        return $this->teamA;
    }

    public function setTeamA(bool $teamA): self
    {
        $this->teamA = $teamA;

        return $this;
    }

    public function isTeamB(): ?bool
    {
        return $this->teamB;
    }

    public function setTeamB(bool $teamB): self
    {
        $this->teamB = $teamB;

        return $this;
    }

    public function isSchiriA(): ?bool
    {
        return $this->schiriA;
    }

    public function setSchiriA(bool $schiriA): self
    {
        $this->schiriA = $schiriA;

        return $this;
    }

    public function isSchiriB(): ?bool
    {
        return $this->schiriB;
    }

    public function setSchiriB(bool $schiriB): self
    {
        $this->schiriB = $schiriB;

        return $this;
    }


}
