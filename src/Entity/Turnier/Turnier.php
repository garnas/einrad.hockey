<?php

namespace App\Entity\Turnier;

use DateTime;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use App\Entity\Team\nTeam;
use App\Entity\Spielplan\SpielplanDetails;

/**
 * Turnier
 *
 * @ORM\Table(name="turniere_liga", indexes={@ORM\Index(name="ausrichter_team_id", columns={"ausrichter"}), @ORM\Index(name="spielplan_vorlage", columns={"spielplan_vorlage"})})
 * @ORM\Entity
 */
class Turnier
{
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
     * @var string|null
     *
     * @ORM\Column(name="tblock_fixed", type="string", length=0, nullable=true, options={"default"="Nein"})
     */
    private ?string $blockFixed = 'Nein';

    /**
     * @var DateTime|null
     *
     * @ORM\Column(name="datum", type="date", nullable=true)
     */
    private ?DateTime $datum;

    /**
     * @var int|null
     *
     * @ORM\Column(name="spieltag", type="integer", nullable=true)
     */
    private int|null $spieltag = 0;

    /**
     * @var string|null
     *
     * @ORM\Column(name="phase", type="string", length=0, nullable=true)
     */
    private ?string $phase;

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
     * @ORM\OneToOne(targetEntity="App\Entity\Turnier\TurnierDetails")
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private TurnierDetails $details;

    /**
     * @var Collection
     *
     * @ORM\OneToMany(targetEntity="App\Entity\Turnier\TurnierErgebnis", mappedBy="turnier")
     * @ORM\JoinColumn(name="turnier_id", referencedColumnName="turnier_id")
     */
    private Collection $ergebnis;

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
    public function getId(): int
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
        $this->block = $block;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getBlockFixed(): ?string
    {
        return $this->blockFixed;
    }

    /**
     * @param string|null $blockFixed
     * @return Turnier
     */
    public function setBlockFixed(?string $blockFixed): Turnier
    {
        $this->blockFixed = $blockFixed;
        return $this;
    }

    /**
     * @return DateTime|null
     */
    public function getDatum(): ?DateTime
    {
        return $this->datum;
    }

    /**
     * @param DateTime|null $datum
     * @return Turnier
     */
    public function setDatum(?DateTime $datum): Turnier
    {
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

}
