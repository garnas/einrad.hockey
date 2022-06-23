<?php

namespace App\Entity\Turnier;

use App\Entity\Spielplan\SpielplanDetails;
use App\Entity\Team\nTeam;
use App\Service\Turnier\TurnierLogService;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Turnier
 *
 * @ORM\Table(name="turniere_liga", indexes={@ORM\Index(name="ausrichter_team_id", columns={"ausrichter"}), @ORM\Index(name="spielplan_vorlage", columns={"spielplan_vorlage"})})
 * @ORM\HasLifecycleCallbacks()
 * @ORM\Entity
 */
class Turnier
{


    public function __construct()
    {
        $this->ergebnis = new ArrayCollection();
        $this->liste = new ArrayCollection();
        $this->logService = new TurnierLogService($this);
    }

    private TurnierLogService $logService;


    /** @ORM\PostLoad() */
    public function setLogService(): void
    {
        $this->logService = new TurnierLogService($this);
    }

    /**
     * @return TurnierLogService
     */
    public function getLogService(): TurnierLogService
    {
        return $this->logService;
    }

    public function isSetzPhase(): bool
    {
        return $this->phase === "setz";
    }


    public function isSpielplanPhase()
    {
        return $this->phase === 'spielplan';
    }

    public function isErgebnisPhase()
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

    public function isFinalTurnier(): bool{
        return $this->getArt() === 'final';
    }

    /**
     * @var int
     *
     * @ORM\Column(name="turnier_id", type="integer", nullable=false)
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="IDENTITY")
     */
    private int $id;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tname", type="string", length=255, nullable=true)
     */
    private ?string $name;

    /**
     * @var string|null
     *
     * @ORM\Column(name="art", type="string", length=0, nullable=true)
     */
    private ?string $art;

    /**
     * @var string|null
     *
     * @ORM\Column(name="tblock", type="string", length=255, nullable=true)
     */
    private ?string $block;

    /**
     * @var DateTime
     *
     * @ORM\Column(name="datum", type="date")
     */
    private DateTime $datum;

    /**
     * @var int|null
     *
     * @ORM\Column(name="spieltag", type="integer", nullable=true)
     */
    private int|null $spieltag = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phase", type="string", nullable=true)
     */
    private ?string $phase;

    /**
     * @var string|null
     *
     * @ORM\Column(name="canceled_grund", type="string", nullable=true)
     */
    private ?string $canceledGrund;

    /**
     * @return string|null
     */
    public function getCanceledGrund(): ?string
    {
        return $this->canceledGrund;
    }

    /**
     * @param string|null $canceledGrund
     * @return Turnier
     */
    public function setCanceledGrund(?string $canceledGrund): Turnier
    {
        $this->canceledGrund = $canceledGrund;
        return $this;
    }



    /**
     * @var string|null
     *
     * @ORM\Column(name="spielplan_datei", type="string", length=255, nullable=true)
     */
    private ?string $spielplanDatei;

    /**
     * @var int|null
     *
     * @ORM\Column(name="saison", type="integer", nullable=true)
     */
    private ?int $saison;

    /**
     * @var SpielplanDetails
     *
     * @ORM\ManyToOne(targetEntity="App\Entity\Spielplan\SpielplanDetails")
     * @ORM\JoinColumns({
     *   @ORM\JoinColumn(name="spielplan_vorlage", referencedColumnName="spielplan")
     * })
     */
    private SpielplanDetails $spielplanVorlage;

    /**
     * @var nTeam
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Team\nTeam")
     * @ORM\JoinColumn(name="ausrichter", referencedColumnName="team_id")
     */
    private nTeam $ausrichter;

    /**
     * @var TurnierDetails
     *
     * @ORM\OneToOne(targetEntity="App\Entity\Turnier\TurnierDetails", mappedBy="turnier", cascade={"persist"})
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private TurnierDetails $details;

    /**
     * @var Collection
     * @ORM\OneToMany(targetEntity="App\Entity\Turnier\TurniereLog", mappedBy="turnier", cascade={"persist"})
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private Collection $logs;


    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Turnier\TurnierErgebnis", mappedBy="turnier", cascade={"persist"})
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private Collection $ergebnis;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Turnier\TurniereListe", mappedBy="turnier", cascade={"persist"})
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private Collection $liste;

    /**
     * @var bool
     * @ORM\Column(name="canceled", type="boolean")
     */
    private bool $canceled;

    /**
     * @return bool
     */
    public function isCanceled(): bool
    {
        return $this->canceled;
    }

    /**
     * @param bool $canceled
     * @return Turnier
     */
    public function setCanceled(bool $canceled): Turnier
    {
        $this->canceled = $canceled;
        return $this;
    }

    /**
     * @return Collection
     */
    public function getListe(): Collection
    {
        return $this->liste;
    }

    /**
     * @param Collection $liste
     * @return Turnier
     */
    public function setListe(Collection $liste): Turnier
    {
        $this->liste = $liste;
        return $this;
    }

    /**
     * @return Collection|TurnierErgebnis[]
     */
    public function getErgebnis(): Collection|array
    {
        return $this->ergebnis;
    }

    /**
     * @param Collection $ergebnis
     * @return Turnier
     */
    public function setErgebnis(Collection $ergebnis): Turnier
    {
        $this->ergebnis = $ergebnis;
        return $this;
    }

    /**
     * @return int
     */
    public function id(): int
    {
        return $this->id;
    }

    /**
     * @param int $id
     * @return Turnier
     */
    public function setId(int $id): Turnier
    {
        $this->id = $id;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @param string|null $name
     * @return Turnier
     */
    public function setName(?string $name): Turnier
    {
        $this->logService->autoLog("Turniername", $this->name, $name);
        $this->name = $name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getArt(): ?string
    {
        return $this->art;
    }

    /**
     * @param string|null $art
     * @return Turnier
     */
    public function setArt(?string $art): Turnier
    {
        $this->logService->autoLog("Art", $this->art, $art);
        $this->art = $art;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBlock(): ?string
    {
        return $this->block;
    }

    /**
     * @param string|null $block
     * @return Turnier
     */
    public function setBlock(?string $block): Turnier
    {
        $this->logService->autoLog("Block", $this->block, $block);
        $this->block = $block;
        return $this;
    }

    /**
     * @return DateTime
     */
    public function getDatum(): DateTime
    {
        return $this->datum;
    }

    /**
     * @param DateTime $datum
     * @return Turnier
     */
    public function setDatum(DateTime $datum): Turnier
    {
        $this->logService->autoLog("Datum", $this->datum, $datum);
        $this->datum = $datum;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSpieltag(): ?int
    {
        return $this->spieltag;
    }

    /**
     * @param int|null $spieltag
     * @return Turnier
     */
    public function setSpieltag(?int $spieltag): Turnier
    {
        $this->logService->autoLog("Spieltag", $this->spieltag, $spieltag);
        $this->spieltag = $spieltag;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getPhase(): ?string
    {
        return $this->phase;
    }

    /**
     * @param string|null $phase
     * @return Turnier
     */
    public function setPhase(?string $phase): Turnier
    {
        $this->logService->autoLog("Phase", $this->phase, $phase);
        $this->phase = $phase;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getSpielplanDatei(): ?string
    {
        return $this->spielplanDatei;
    }

    /**
     * @param string|null $spielplanDatei
     * @return Turnier
     */
    public function setSpielplanDatei(?string $spielplanDatei): Turnier
    {
        $this->logService->autoLog("Spielplandatei", $this->spielplanDatei, $spielplanDatei);
        $this->spielplanDatei = $spielplanDatei;
        return $this;
    }

    /**
     * @return int|null
     */
    public function getSaison(): ?int
    {
        return $this->saison;
    }

    /**
     * @param int|null $saison
     * @return Turnier
     */
    public function setSaison(?int $saison): Turnier
    {
        $this->logService->autoLog("Saison", $this->saison, $saison);
        $this->saison = $saison;
        return $this;
    }

    /**
     * @return SpielplanDetails
     */
    public function getSpielplanVorlage(): SpielplanDetails
    {
        return $this->spielplanVorlage;
    }

    /**
     * @param SpielplanDetails $spielplanVorlage
     * @return Turnier
     */
    public function setSpielplanVorlage(SpielplanDetails $spielplanVorlage): Turnier
    {
        $this->logService->autoLog("Spielplanvorlage", $this->spielplanVorlage, $spielplanVorlage);
        $this->spielplanVorlage = $spielplanVorlage;
        return $this;
    }

    /**
     * @return nTeam
     */
    public function getAusrichter(): nTeam
    {
        return $this->ausrichter;
    }

    /**
     * @param nTeam $ausrichter
     * @return Turnier
     */
    public function setAusrichter(nTeam $ausrichter): Turnier
    {
        $this->logService->autoLog("Ausrichter", $this->ausrichter->getName(), $ausrichter->getName());
        $this->ausrichter = $ausrichter;
        return $this;
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

    /**
     * @return Collection
     */
    public function getLogs(): Collection
    {
        return $this->logs;
    }

    /**
     * @param Collection $logs
     */
    public function setLogs(Collection $logs): void
    {
        $this->logs = $logs;
    }

    public function isLigaturnier(): bool
    {
        return $this->art == 'I' || $this->art == 'II';
    }

}
