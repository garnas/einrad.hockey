<?php

namespace App\Entity\Team;

use App\Entity\Turnier\Turnier;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Tabelle;
use Helper;
use Config;

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
     * @ORM\OneToMany(targetEntity="App\Entity\Turnier\TurniereListe", mappedBy="team", cascade={"all"}, indexBy="turnier_id")
     */
    private Collection $turniereListe;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Turnier\Turnier", mappedBy="ausrichter", cascade={"all"}, indexBy="turnier_id")
     */
    private Collection $ausgerichteteTurniere;

    /**
     * @return Collection|Turnier[]
     */
    public function getAusgerichteteTurniere(): Collection|array
    {
        return $this->ausgerichteteTurniere;
    }

    /**
     * @param Collection $ausgerichteteTurniere
     * @return nTeam
     */
    public function setAusgerichteteTurniere(Collection $ausgerichteteTurniere): nTeam
    {
        $this->ausgerichteteTurniere = $ausgerichteteTurniere;
        return $this;
    }

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Team\Freilos", mappedBy="team", cascade={"all"})
     */
    private Collection $freilose;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Team\Kontakt", mappedBy="team")
     */
    private Collection $emails;

    /**
     * @return Collection
     */
    public function getEmails(): Collection
    {
        return $this->emails;
    }

    public function getEmailsByAutoInfo(): Collection
    {
        $filter = static function (Kontakt $f) {
            return $f->getGetInfoMail() == "Ja";
        };
        return $this->emails->filter($filter);
    }

    /**
     * @param Collection $emails
     * @return nTeam
     */
    public function setEmails(Collection $emails): nTeam
    {
        $this->emails = $emails;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getTurniereListe(): Collection
    {
        return $this->turniereListe;
    }

    /**
     * @param Collection $turniereListe
     */
    public function setTurniere(Collection $turniereListe): void
    {
        $this->turniereListe = $turniereListe;
    }

    public function __construct() {
        $this->turniereListe = new ArrayCollection();
        $this->ausgerichteteTurniere = new ArrayCollection();
    }


    /**
     * @var TeamDetails
     *
     * @ORM\OneToOne(targetEntity="TeamDetails", cascade={"all"}, orphanRemoval=true)
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private TeamDetails $details;

    /**
     * @return TeamDetails
     */
    public function getDetails(): TeamDetails
    {
        return $this->details;
    }

    /**
     * @param TeamDetails $details
     */
    public function setDetails(TeamDetails $details): void
    {
        $this->details = $details;
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
    private $freilose_old;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="zweites_freilos", type="date", nullable=true, options={"comment"="2 Schiris 2 Freilose"})
     */
    private ?DateTime $zweitesFreilos;

    /**
     * @var string
     *
     * @ORM\Column(name="aktiv", type="string", length=0, nullable=false, options={"default"="Ja"})
     */
    private $aktiv = 'Ja';

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Team\Spieler", mappedBy="team", cascade={"all"})
     * @ORM\JoinColumn(name="team_id", referencedColumnName="team_id")
     */
    private Collection $kader;

    /**
     * @return Collection
     */
    public function getKader(): Collection
    {
        return $this->kader;
    }

    /**
     * @param Collection $kader
     * @return nTeam
     */
    public function setKader(Collection $kader): nTeam
    {
        $this->kader = $kader;
        return $this;
    }



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
        $passwort_hash = password_hash($passwort, PASSWORD_DEFAULT);
        if (!is_string($passwort)) {
            trigger_error("set_passwort fehlgeschlagen.", E_USER_ERROR);
        }

        // Befindet sich das Team im Teamcenter ihr Passwort geändert?
        $pw_geaendert = (Helper::$teamcenter) ? 'Ja' : 'Nein';
        $this->setPasswortGeaendert($pw_geaendert);
        $this->passwort = $passwort_hash;

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

    public function getFreiloseOld(): ?int
    {
        return $this->freilose_old;
    }

    public function setFreiloseOld(?int $freilose_old): self
    {
        $this->freilose_old = $freilose_old;

        return $this;
    }

    public function getZweitesFreilos(): ?DateTime
    {
        return $this->zweitesFreilos;
    }

    public function setZweitesFreilos(?DateTime $zweitesFreilos): self
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

    public function getBlock(int $spieltag = null): ?string
    {
        if (!$this->isLigaTeam()) {
            return null;
        }
        return Tabelle::get_team_block($this->id(), $spieltag); // TODO Symfonyfy
    }

    public function isLigaTeam(): bool
    {
        return $this->ligateam === 'Ja';
    }

    public function isAktiv(): bool
    {
        return $this->aktiv === 'Ja';
    }

    public function addFreilos(FreilosGrund $grund, int $saison = Config::SAISON): nTeam
    {
        $freilos = new Freilos();
        $freilos->setTeam($this);
        $freilos->setErstelltAm();
        $freilos->setGrund($grund);
        $freilos->setSaison($saison);
        $this->freilose[] = $freilos;
        return $this;
    }

    /**
     * @return Freilos[]|Collection
     */
    public function getGueltigeFreilose(): Collection|array
    {
        $filter = static function (Freilos $f) {
            return $f->isGueltig();
        };
        return $this->freilose->filter($filter);
    }

    /**
     * @return Freilos[]|Collection
     */
    public function getFreiloseBySaison(int $saison = Config::SAISON): Collection|array
    {
        $filter = static function (Freilos $f) use ($saison) {
            return $f->getSaison() == $saison;
        };
        return $this->freilose->filter($filter);
    }

    /**
     * @return Collection|Freilos[]
     */
    public function getOffeneFreilose(): Collection|array
    {
        $filter = static function (Freilos $f) {
            return !$f->isGesetzt() && $f->isGueltig();
        };
        return $this->freilose->filter($filter);
    }

    public function getAnzahlOffenerFreilose(): int
    {

        return $this->getOffeneFreilose()->count();
    }

    public function getOldestFreilos(): Freilos
    {
        $freilose = $this->getGueltigeFreilose()->toArray();
        usort($freilose, static function(Freilos $a, Freilos $b) {
            return $a->getErstelltAm() <=> $b->getErstelltAm();
        });
        return $freilose[0];
    }

}
