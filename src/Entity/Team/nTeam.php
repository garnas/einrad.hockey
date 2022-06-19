<?php

namespace App\Entity\Team;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * TeamsLiga
 *
 * @ORM\Table(name="teams_liga", uniqueConstraints={@ORM\UniqueConstraint(name="teamname", columns={"teamname"})})
 * @ORM\Entity
 */
class nTeam
{
    /**
     * @var int
     *
     * @ORM\Column(name="team_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="teamname", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Turnier\TurniereListe", mappedBy="teamsLiga")
     */
    private Collection $turniere_liste;

    /**
     * @return Collection
     */
    public function get_turniere_liste(): Collection
    {
        return $this->turniere_liste;
    }

    /**
     * @param Collection $turniere_liste
     */
    public function set_turniere_liste(Collection $turniere_liste): void
    {
        $this->turniere_liste = $turniere_liste;
    }

    public function __construct() {
        $this->turniere_liste = new \Doctrine\Common\Collections\ArrayCollection();
    }


    /**
     * @var TeamDetails
     *
     * @ORM\OneToOne(targetEntity="TeamDetails")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private TeamDetails $teams_details;

    /**
     * @return TeamDetails
     */
    public function get_teams_details(): TeamDetails
    {
        return $this->teams_details;
    }

    /**
     * @param TeamDetails $teams_details
     */
    public function set_teams_details(TeamDetails $teams_details): void
    {
        $this->teams_details = $teams_details;
    }

    /**
     * @var string
     *
     * @ORM\Column(name="ligateam", type="string", length=0, nullable=false, options={"default"="Ja"})
     */
    private $ligateam = 'Ja';

    /**
     * @var string
     *
     * @ORM\Column(name="terminplaner", type="string", length=0, nullable=false, options={"default"="Nein"})
     */
    private $terminplaner = 'Nein';

    /**
     * @var string|null
     *
     * @ORM\Column(name="passwort", type="string", length=255, nullable=true)
     */
    private $passwort;

    /**
     * @var string
     *
     * @ORM\Column(name="passwort_geaendert", type="string", length=0, nullable=false, options={"default"="Nein"})
     */
    private $passwortGeaendert = 'Nein';

    /**
     * @var int|null
     *
     * @ORM\Column(name="freilose", type="integer", nullable=true)
     */
    private $freilose;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(name="zweites_freilos", type="date", nullable=true, options={"comment"="2 Schiris 2 Freilose"})
     */
    private $zweitesFreilos;

    /**
     * @var string
     *
     * @ORM\Column(name="aktiv", type="string", length=0, nullable=false, options={"default"="Ja"})
     */
    private $aktiv = 'Ja';

    public function id(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getLigateam(): ?string
    {
        return $this->ligateam;
    }

    public function setLigateam(string $ligateam): self
    {
        $this->ligateam = $ligateam;

        return $this;
    }

    public function getTerminplaner(): ?string
    {
        return $this->terminplaner;
    }

    public function setTerminplaner(string $terminplaner): self
    {
        $this->terminplaner = $terminplaner;

        return $this;
    }

    public function getPasswort(): ?string
    {
        return $this->passwort;
    }

    public function setPasswort(?string $passwort): self
    {
        $this->passwort = $passwort;

        return $this;
    }

    public function getPasswortGeaendert(): ?string
    {
        return $this->passwortGeaendert;
    }

    public function setPasswortGeaendert(string $passwortGeaendert): self
    {
        $this->passwortGeaendert = $passwortGeaendert;

        return $this;
    }

    public function getFreilose(): ?int
    {
        return $this->freilose;
    }

    public function setFreilose(?int $freilose): self
    {
        $this->freilose = $freilose;

        return $this;
    }

    public function getZweitesFreilos(): ?\DateTimeInterface
    {
        return $this->zweitesFreilos;
    }

    public function setZweitesFreilos(?\DateTimeInterface $zweitesFreilos): self
    {
        $this->zweitesFreilos = $zweitesFreilos;

        return $this;
    }

    public function getAktiv(): ?string
    {
        return $this->aktiv;
    }

    public function setAktiv(string $aktiv): self
    {
        $this->aktiv = $aktiv;

        return $this;
    }

}
