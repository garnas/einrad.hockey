<?php

namespace App\Entity\Team;

use Doctrine\ORM\Mapping as ORM;
use TeamsLiga;

/**
 * TeamsKontakt
 *
 * @ORM\Table(name="teams_kontakt", indexes={@ORM\Index(name="team_id", columns={"team_id"})})
 * @ORM\Entity
 */
class Kontakt
{
    /**
     * @var int
     *
     * @ORM\Column(name="teams_kontakt_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="email", type="string", length=255, nullable=false)
     */
    private string $email;

    /**
     * @var string
     *
     * @ORM\Column(name="public", type="string", length=0, nullable=false, options={"default"="Ja"})
     */
    private string $public = 'Ja';

    /**
     * @var string
     *
     * @ORM\Column(name="get_info_mail", type="string", length=0, nullable=false, options={"default"="Ja"})
     */
    private string $getInfoMail = 'Ja';

    /**
     * @var nTeam
     *
     * @ORM\ManyToOne(targetEntity="nTeam")
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private nTeam $team;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }

    public function getPublic(): ?string
    {
        return $this->public;
    }

    public function setPublic(string $public): self
    {
        $this->public = $public;

        return $this;
    }

    public function getGetInfoMail(): ?string
    {
        return $this->getInfoMail;
    }

    public function setGetInfoMail(string $getInfoMail): self
    {
        $this->getInfoMail = $getInfoMail;

        return $this;
    }

    public function getTeam(): nTeam
    {
        return $this->team;
    }

    public function setTeam(nTeam $team): self
    {
        $this->team = $team;

        return $this;
    }


}
