<?php

namespace App\Entity\Team;

use App\Entity\Turnier\Turnier;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity]
#[ORM\Table(name: "teams_strafen", uniqueConstraints: [new ORM\UniqueConstraint(name: "teamname", columns: ["teamname"])])]
class Strafe
{

    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "strafe_id", type: "integer", nullable: false)]
    private int $strafeId;

    #[ORM\Column(name: "verwarnung", type: "string", length: 255, nullable: true)]
    private ?string $verwarnung;

    #[ORM\Column(name: "grund", type: "string", length: 255, nullable: false)]
    private string $grund;

    #[ORM\Column(name: "prozentsatz", type: "string", length: 255, nullable: true)]
    private ?string $prozentsatz;

    #[ORM\Column(name: "saison", type: "integer", length: 255, nullable: false)]
    private int $saison;

    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id")]
    #[ORM\ManyToOne(targetEntity: nTeam::class, inversedBy: "strafen")]
    private nTeam $team;

    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id", nullable: true)]
    #[ORM\ManyToOne(targetEntity: Turnier::class)]
    private ?Turnier $turnier;

    /**
     * @return int
     */
    public function getStrafeId(): int
    {
        return $this->strafeId;
    }

    /**
     * @param int $strafeId
     * @return Strafe
     */
    public function setStrafeId(int $strafeId): Strafe
    {
        $this->strafeId = $strafeId;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getVerwarnung(): ?string
    {
        return $this->verwarnung;
    }

    /**
     * @return string|null
     */
    public function isVerwarnung(): ?string
    {
        return $this->verwarnung == "Ja";
    }

    public function isStrafe(): ?string
    {
        return $this->verwarnung == "Nein";
    }

    /**
     * @param string|null $verwarnung
     * @return Strafe
     */
    public function setVerwarnung(?string $verwarnung): Strafe
    {
        $this->verwarnung = $verwarnung;
        return $this;
    }

    /**
     * @return string
     */
    public function getGrund(): string
    {
        return $this->grund;
    }

    /**
     * @param string $grund
     * @return Strafe
     */
    public function setGrund(string $grund): Strafe
    {
        $this->grund = $grund;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getProzentsatz(): ?string
    {
        return $this->prozentsatz;
    }

    /**
     * @param string|null $prozentsatz
     * @return Strafe
     */
    public function setProzentsatz(?string $prozentsatz): Strafe
    {
        $this->prozentsatz = $prozentsatz;
        return $this;
    }

    /**
     * @return int
     */
    public function getSaison(): int
    {
        return $this->saison;
    }

    /**
     * @param int $saison
     * @return Strafe
     */
    public function setSaison(int $saison): Strafe
    {
        $this->saison = $saison;
        return $this;
    }

    /**
     * @return nTeam
     */
    public function getTeam(): nTeam
    {
        return $this->team;
    }

    /**
     * @param nTeam $team
     * @return Strafe
     */
    public function setTeam(nTeam $team): Strafe
    {
        $this->team = $team;
        return $this;
    }

    /**
     * @return Turnier|null
     */
    public function getTurnier(): ?Turnier
    {
        return $this->turnier;
    }

    /**
     * @param Turnier|null $turnier
     * @return Strafe
     */
    public function setTurnier(?Turnier $turnier): Strafe
    {
        $this->turnier = $turnier;
        return $this;
    }

}
