<?php

namespace App\Entity\Turnier;

use App\Entity\Spielplan\SpielplanDetails;
use App\Entity\Team\nTeam;
use App\Service\Turnier\TurnierLogService;
use App\Service\Turnier\TurnierSnippets;
use DateTime;
use Discord;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Turnier
 *
 */
#[ORM\Entity]
#[ORM\HasLifecycleCallbacks]
#[ORM\Table(
    name: "turniere_liga",
    indexes: [
        new ORM\Index(columns: ["ausrichter"], name: "ausrichter_team_id"),
        new ORM\Index(columns: ["spielplan_vorlage"], name: "spielplan_vorlage")
    ])
]
class Turnier
{

    private TurnierLogService $logService;

    #[ORM\GeneratedValue(strategy: "IDENTITY")]
    #[ORM\Id]
    #[ORM\Column(name: "turnier_id", type: "integer", nullable: false)]
    private int $id;

    #[ORM\Column(name: "tname", type: "string", length: 255, nullable: true)]
    private ?string $name;

    #[ORM\Column(name: "art", type: "string", length: 0, nullable: true)]
    private ?string $art;

    #[ORM\Column(name: "tblock", type: "string", length: 255, nullable: true)]
    private ?string $block;

    #[ORM\Column(name: "datum", type: "date")]
    private ?DateTime $datum;

    #[ORM\Column(name: "spieltag", type: "integer", nullable: true)]
    private int|null $spieltag = 0;

    #[ORM\Column(name: "phase", type: "string", nullable: true)]
    private ?string $phase;

    #[ORM\Column(name: "canceled_grund", type: "string", nullable: true)]
    private ?string $canceledGrund;

    #[ORM\Column(name: "spielplan_datei", type: "string", length: 255, nullable: true)]
    private ?string $spielplanDatei;
    /**
     * @var int|null
     *
     */
    #[ORM\Column(name: "saison", type: "integer", nullable: true)]
    private ?int $saison;

    #[ORM\ManyToOne(targetEntity: "App\Entity\Spielplan\SpielplanDetails")]
    #[ORM\JoinColumn(name: "spielplan_vorlage", referencedColumnName: "spielplan")]
    private SpielplanDetails $spielplanVorlage;

    #[ORM\JoinColumn(name: "ausrichter", referencedColumnName: "team_id")]
    #[ORM\OneToOne(targetEntity: "App\Entity\Team\\nTeam")]
    private nTeam $ausrichter;

    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    #[ORM\OneToOne(inversedBy: "turnier", targetEntity: "App\Entity\Turnier\TurnierDetails", cascade: ["all"])]
    private TurnierDetails $details;

    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    #[ORM\OneToMany(mappedBy: "turnier", targetEntity: "App\Entity\Turnier\TurniereLog", cascade: ["all"])]
    private Collection $logs;

    #[ORM\Column(name: "erstellt_am", type: "datetime")]
    private DateTime $erstelltAm;

    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    #[ORM\OneToMany(mappedBy: "turnier", targetEntity: "App\Entity\Turnier\TurnierErgebnis", cascade: ["all"], orphanRemoval: true)]
    private Collection $ergebnis;

    #[ORM\OneToMany(mappedBy: "turnier", targetEntity: "App\Entity\Turnier\TurniereListe", cascade: ["all"], orphanRemoval: true, indexBy: "team_id")]
    #[ORM\JoinColumn(name: "turnier_id", referencedColumnName: "turnier_id")]
    private Collection $liste;

    #[ORM\Column(name: "canceled", type: "boolean")]
    private bool $canceled;

    #[ORM\Column(name: "sofort_oeffnen", type: "boolean")]
    private bool $sofortOeffnen;

    public function isSofortOeffnen(): bool
    {
        return $this->sofortOeffnen;
    }

    public function setSofortOeffnen(bool $sofortOeffnen): Turnier
    {
        $this->logService->autoLog("Sofort öffnen nach dem Phasenwechsel",
            $this->sofortOeffnen ?? false,
            $sofortOeffnen);
        $this->sofortOeffnen = $sofortOeffnen;
        return $this;
    }


    public function __construct()
    {
        $this->ergebnis = new ArrayCollection();
        $this->liste = new ArrayCollection();
        $this->logs = new ArrayCollection();
        $this->logService = new TurnierLogService($this);
    }

    #[ORM\PreFlush]
    public function saveLogs(): void
    {
        $this->logService->addAllLogs();
        if (!empty($this->logService->getLogAsString())) {
            Discord::send(TurnierSnippets::ortDatumBlock($this, false) . "\r\n" . $this->logService->getLogAsString());
        }
    }

    /**
     * @return TurnierLogService
     */
    public function getLogService(): TurnierLogService
    {
        return $this->logService;
    }

    #[ORM\PostLoad]
    public function setLogService(): void
    {
        $this->logService = new TurnierLogService($this);
    }

    public function isSetzPhase(): bool
    {
        return $this->phase === "setz";
    }

    public function isSpielplanPhase(): bool
    {
        return $this->phase === 'spielplan';
    }

    public function isErgebnisPhase(): bool
    {
        return $this->phase === 'ergebnis';
    }

    public function isSpassTurnier(): bool
    {
        return $this->art === "spass";
    }

    public function isWartePhase(): bool
    {
        return $this->phase === "warte";
    }

    public function hasBesprechung(): bool
    {
        return $this->getDetails()->getBesprechung() === "Ja";
    }

    /**
     * @return TurnierDetails
     */
    public function getDetails(): TurnierDetails
    {
        return $this->details;
    }

    /**
     * @param TurnierDetails $details
     * @return Turnier
     */
    public function setDetails(TurnierDetails $details): Turnier
    {
        $this->details = $details;
        return $this;
    }

    public function isFinalTurnier(): bool
    {
        return $this->getArt() === 'final';
    }

    public function getArt(): ?string
    {
        return $this->art;
    }

    public function setArt(?string $art): Turnier
    {
        $this->logService->autoLog("Art", $this->art ?? '', $art);
        $this->art = $art;
        return $this;
    }

    public function getCanceledGrund(): ?string
    {
        return $this->canceledGrund;
    }

    public function setCanceledGrund(?string $canceledGrund): Turnier
    {
        $this->canceledGrund = $canceledGrund;
        return $this;
    }

    public function getErstelltAm(): DateTime
    {
        return $this->erstelltAm;
    }

    public function setErstelltAm(DateTime $erstelltAm): Turnier
    {
        $this->erstelltAm = $erstelltAm;
        return $this;
    }

    public function isCanceled(): bool
    {
        return $this->canceled;
    }

    public function setCanceled(bool $canceled): Turnier
    {
        $this->canceled = $canceled;
        return $this;
    }

    public function getListe(): Collection|array
    {
        return $this->liste;
    }

    public function setListe(Collection $liste): Turnier
    {
        $this->liste = $liste;
        return $this;
    }

    public function getErgebnis(): Collection|array
    {
        return $this->ergebnis;
    }

    public function setErgebnis(Collection $ergebnis): Turnier
    {
        $this->ergebnis = $ergebnis;
        return $this;
    }

    public function id(): ?int
    {
        return $this->id ?? null;
    }

    public function setId(int $id): Turnier
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(?string $name): Turnier
    {
        $this->logService->autoLog("Turniername", $this->name ?? '', $name);
        $this->name = $name;
        return $this;
    }

    public function getBlock(): ?string
    {
        return $this->block;
    }

    public function setBlock(?string $block): Turnier
    {
        $this->logService->autoLog("Block", $this->block ?? null, $block);
        $this->block = $block;
        return $this;
    }

    public function getDatum(): DateTime
    {
        return $this->datum;
    }

    public function setDatum(DateTime $datum): Turnier
    {
        $this->logService->autoLog("Datum", $this->datum ?? null, $datum->format("d.m.Y"));
        $this->datum = $datum;
        return $this;
    }

    public function getSpieltag(): ?int
    {
        return $this->spieltag;
    }

    public function setSpieltag(?int $spieltag): Turnier
    {
        $this->logService->autoLog("Spieltag", $this->spieltag ?? null, $spieltag);
        $this->spieltag = $spieltag;
        return $this;
    }

    public function getPhase(): ?string
    {
        return $this->phase;
    }

    public function setPhase(?string $phase): Turnier
    {
        $this->logService->autoLog("Phase", $this->phase ?? null, $phase);
        $this->phase = $phase;
        return $this;
    }

    public function getSpielplanDatei(): ?string
    {
        return $this->spielplanDatei;
    }

    public function setSpielplanDatei(?string $spielplanDatei): Turnier
    {
        $this->logService->autoLog("Spielplandatei", $this->spielplanDatei ?? null, $spielplanDatei);
        $this->spielplanDatei = $spielplanDatei;
        return $this;
    }

    public function getSaison(): ?int
    {
        return $this->saison;
    }

    public function setSaison(?int $saison): Turnier
    {
        $this->logService->autoLog("Saison", $this->saison ?? null, $saison);
        $this->saison = $saison;
        return $this;
    }

    public function getSpielplanVorlage(): ?SpielplanDetails
    {
        return $this->spielplanVorlage ?? null;
    }

    public function setSpielplanVorlage(SpielplanDetails $spielplanVorlage): Turnier
    {
        $this->logService->autoLog("Spielplanvorlage", $this->spielplanVorlage ?? null, $spielplanVorlage);
        $this->spielplanVorlage = $spielplanVorlage;
        return $this;
    }

    public function getAusrichter(): nTeam
    {
        return $this->ausrichter;
    }

    public function setAusrichter(nTeam $ausrichter): Turnier
    {
        $ausrichterText = isset ($this->ausrichter) ? $this->ausrichter->getName() : null;
        $this->logService->autoLog("Ausrichter", $ausrichterText, $ausrichter->getName());
        $this->ausrichter = $ausrichter;
        return $this;
    }

    public function getLogs(): Collection|array
    {
        return $this->logs;
    }

    public function setLogs(Collection $logs): void
    {
        $this->logs = $logs;
    }

    public function isLigaturnier(): bool
    {
        return $this->art == 'I' || $this->art == 'II';
    }

}
