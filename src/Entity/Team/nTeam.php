<?php

namespace App\Entity\Team;

use App\Entity\Turnier\Turnier;
use App\Entity\Turnier\TurniereListe;
use App\Entity\Turnier\TurnierErgebnis;
use Config;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Helper;
use Tabelle;

#[ORM\Entity]
#[ORM\Table(name: "teams_liga", uniqueConstraints: [new ORM\UniqueConstraint(name: "teamname", columns: ["teamname"])])]
class nTeam
{

    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "team_id", type: "integer", nullable: false)]
    private int $id;

    #[ORM\Column(name: "teamname", type: "string", length: 255, nullable: false)]
    private string $name;

    #[ORM\OneToMany(targetEntity: TurniereListe::class, mappedBy: "team", cascade: ["all"], indexBy: "turnier_id")]
    private Collection $turniereListe;

    #[ORM\OneToMany(targetEntity: Turnier::class, mappedBy: "ausrichter", cascade: ["all"], indexBy: "turnier_id")]
    private Collection $ausgerichteteTurniere;

    #[ORM\OneToMany(targetEntity: TurnierErgebnis::class, mappedBy: "team", cascade: ["all"], indexBy: "turnier_id")]
    private Collection $ergebnisse;

    #[ORM\OneToMany(targetEntity: Strafe::class, mappedBy: "team", cascade: ["all"], indexBy: "strafe_id")]
    private Collection $strafen;

    public function getAusgerichteteTurniere(): Collection|array
    {
        return $this->ausgerichteteTurniere;
    }

    public function setAusgerichteteTurniere(Collection $ausgerichteteTurniere): nTeam
    {
        $this->ausgerichteteTurniere = $ausgerichteteTurniere;
        return $this;
    }

    #[ORM\OneToMany(targetEntity: Freilos::class, mappedBy: "team", cascade: ["all"])]
    private Collection $freilose;

    #[ORM\OneToMany(targetEntity: Kontakt::class, mappedBy: "team", cascade: ["all"])]
    private Collection $emails;

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

    public function setEmails(Collection $emails): nTeam
    {
        $this->emails = $emails;
        return $this;
    }

    public function getTurniereListe(): Collection
    {
        return $this->turniereListe;
    }

    public function setTurniere(Collection $turniereListe): nTeam
    {
        $this->turniereListe = $turniereListe;
        return $this;
    }

    public function __construct() {
        $this->turniereListe = new ArrayCollection();
        $this->ausgerichteteTurniere = new ArrayCollection();
        $this->freilose = new ArrayCollection();
        $this->emails = new ArrayCollection();
        $this->ergebnisse = new ArrayCollection();
        $this->strafen = new ArrayCollection();
    }

    #[ORM\OneToOne(targetEntity: TeamDetails::class, mappedBy: "team", cascade: ["all"])]
    private TeamDetails $details;

    public function getDetails(): TeamDetails
    {
        return $this->details;
    }

    public function setDetails(TeamDetails $details): nTeam
    {
        $this->details = $details;
        return $this;
    }

    #[ORM\Column(name: "ligateam", type: "string", length: 0, nullable: false, options: ["default" => "Ja"])]
    private string $ligateam = 'Ja';

    #[ORM\Column(name: "terminplaner", type: "string", length: 0, nullable: false, options: ["default" => "Nein"])]
    private string $terminplaner = 'Nein';

    #[ORM\Column(name: "passwort", type: "string", length: 255, nullable: true)]
    private ?string $passwort;

    #[ORM\Column(name: "passwort_geaendert", type: "string", length: 0, nullable: false, options: ["default" => "Nein"])]
    private string $passwortGeaendert = 'Nein';

    #[ORM\Column(name: "aktiv", type: "string", length: 0, nullable: false, options: ["default" => "Ja"])]
    private string $aktiv = 'Ja';

    #[ORM\JoinColumn(name: "team_id", referencedColumnName: "team_id")]
    #[ORM\OneToMany(targetEntity: Spieler::class, mappedBy: "team", cascade: ["all"])]
    private Collection $kader;

    public function getKader(): Collection
    {
        return $this->kader;
    }

    /**
     * @return Collection|Spieler[]
     */
    public function getKaderAktuell(): Collection|array
    {
        $filter = static function (Spieler $s) {
            return $s->getLetzteSaison() == Config::SAISON;
        };
        return $this->kader->filter($filter);
    }

    /**
     * @return Collection|Spieler[]
     */
    public function getKaderVorsaison(): Collection|array
    {
        $filter = static function (Spieler $s) {
            return $s->getLetzteSaison() < Config::SAISON and $s->getLetzteSaison() >= (Config::SAISON - 2);
        };
        return $this->kader->filter($filter);
    }

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

    public function addFreilos(
        FreilosGrund $grund,
        int $saison = Config::SAISON,
        ?Turnier $turnierAusgerichtet = null,
        ?Freilos $vorherigesFreilos = null
    ): nTeam
    {
        $freilos = (new Freilos())
            ->setTeam($this)
            ->setErstelltAm()
            ->setGrund($grund)
            ->setTurnierAusgerichtet($turnierAusgerichtet)
            ->setVorherigesFreilos($vorherigesFreilos)
            ->setSaison($saison);
        $this->freilose[] = $freilos;
        return $this;
    }

    /**
     * @return Collection|array|Freilos[]
     */
    public function getGueltigeFreilose(): Collection|array
    {
        $filter = static function (Freilos $f) {
            return $f->isGueltig();
        };
        return $this->freilose->filter($filter);
    }

    /**
     * @return Collection|array|Freilos[]
     */
    public function getFreiloseBySaison(int $saison = Config::SAISON): Collection|array
    {
        $filter = static function (Freilos $f) use ($saison) {
            return $f->getSaison() == $saison;
        };
        return $this->freilose->filter($filter);
    }

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

    /**
     * Gibt das älteste Freilos zurück, welches als nächstes gesetzt werden soll.
     */
    public function getNextFreilos(): Freilos
    {
        $freilose = $this->getOffeneFreilose()->toArray();
        usort($freilose, static function(Freilos $a, Freilos $b) {
            if ($a->getSaison() == $b->getSaison()) {
                return $a->getErstelltAm() <=> $b->getErstelltAm();
            }
            return $a->getSaison() <=> $b->getSaison();
        });
        return $freilose[0];
    }

    /**
     * @return Collection|array|Freilos[]
     */
    public function getGesetzteFreilose(): Collection|array
    {
        $filter = static function (Freilos $f) {
            return $f->isGesetzt() && $f->isGueltig();
        };
        return $this->freilose->filter($filter);
    }

    public function addEmail(Kontakt $kontakt)
    {
        $this->emails->add($kontakt);
    }

    public function setErgebnisse(Collection $ergebnisse): nTeam
    {
        $this->ergebnisse = $ergebnisse;
        return $this;
    }

    /**
     * @return Collection|TurnierErgebnis[]
     */
    public function getErgebnisse(): Collection | array
    {
        return $this->ergebnisse;
    }

    /**
     * @return Collection|TurnierErgebnis[]
     */
    public function getErgebnisseVorsaison(): Collection | array
    {
        return $this->ergebnisse->filter(
            fn (TurnierErgebnis $e) => $e->getTurnier()->getSaison() === (Config::SAISON - 1)
        );
    }

    public function setStrafen(Collection $strafen): nTeam
    {
        $this->strafen = $strafen;
        return $this;
    }

    public function getStrafen(): Collection
    {
        return $this->strafen;
    }

}
