<?php

namespace App\Model\Spielplan;

use App\Entity\Spielplan\SpielplanDetails;
use App\Entity\Turnier\Turnier;
use App\Service\Spielplan\TrikotfarbenService;

/**
 * Anzeige-Aggregat eines JgJ-Spielplans: bündelt Turnier, Vorlagendetails, Teamliste,
 * Spiele (mit Anstoßzeit) und die berechnete Platzierung für Templates und XML/PDF.
 *
 * Ersetzt die frühere Klasse Spielplan_JgJ. Erzeugt über
 * {@see \App\Service\Spielplan\SpielplanFactory::ausTurnier()}.
 */
final class Spielplan
{
    /**
     * @param array<int, array> $teamliste keyed by Team-ID ({@see \App\Service\Turnier\TurnierService::getSpielenliste()})
     * @param array<int, SpielAnsicht> $spiele keyed by Spiel-ID
     * @param array<int, int> $pausen Spiel-ID => Minuten Pause danach
     */
    public function __construct(
        private readonly Turnier $turnier,
        private readonly SpielplanDetails $details,
        private readonly array $teamliste,
        private readonly array $spiele,
        private readonly Platzierungsergebnis $platzierung,
        private readonly array $pausen,
    ) {}

    public function getTurnier(): Turnier
    {
        return $this->turnier;
    }

    public function getTurnierId(): int
    {
        return $this->turnier->id();
    }

    public function getDetails(): SpielplanDetails
    {
        return $this->details;
    }

    /**
     * @return array<int, array>
     */
    public function getTeamliste(): array
    {
        return $this->teamliste;
    }

    public function getAnzahlTeams(): int
    {
        return count($this->teamliste);
    }

    /**
     * @return array<int, SpielAnsicht> keyed by Spiel-ID
     */
    public function getSpiele(): array
    {
        return $this->spiele;
    }

    /**
     * Minuten Pause nach dem Spiel mit dieser Spiel-ID.
     */
    public function getPause(int $spielId): int
    {
        return $this->pausen[$spielId] ?? 0;
    }

    /**
     * @return array<int, Platzierung> keyed by Team-ID, in Platzierungsreihenfolge
     */
    public function getPlatzierungstabelle(): array
    {
        return $this->platzierung->platzierungstabelle;
    }

    /**
     * @return array<int, array<int, Turniertabellenzeile>>
     */
    public function getDirekterVergleichTabellen(): array
    {
        return $this->platzierung->direkterVergleichTabellen;
    }

    /**
     * @return array<int, array<int, Turniertabellenzeile>>
     */
    public function getPenaltyTabellen(): array
    {
        return $this->platzierung->penaltyTabellen;
    }

    public function isOutOfScope(): bool
    {
        return $this->platzierung->outOfScope;
    }

    /**
     * Platzierungstabelle im Format, das {@see \App\Service\Turnier\TurnierService::setErgebnisse()} erwartet.
     *
     * @return array<int, array{ligapunkte: int|float, platz: int}>
     */
    public function toErgebnisTabelle(): array
    {
        $tabelle = [];
        foreach ($this->getPlatzierungstabelle() as $teamId => $platzierung) {
            $tabelle[$teamId] = [
                'ligapunkte' => $platzierung->ligapunkte,
                'platz' => $platzierung->platz,
            ];
        }
        return $tabelle;
    }

    /* -------------------------------------------------------------------------
     *  Penalty- und Tabellen-Checks (portiert aus Spielplan_JgJ)
     * ---------------------------------------------------------------------- */

    /**
     * Muss für dieses Spiel ein Penalty gespielt werden?
     */
    public function hatPenaltySpiel(int $spielId, bool $nurAusstehend = false): bool
    {
        $penaltys = $nurAusstehend
            ? $this->platzierung->penaltys->ausstehend
            : $this->platzierung->penaltys->gesamt;

        return in_array($spielId, $penaltys);
    }

    /**
     * Muss das Team noch einen (ausstehenden) Penalty spielen?
     */
    public function hatPenaltyTeam(int $teamId): bool
    {
        foreach ($this->platzierung->penaltys->ausstehend as $spielId) {
            $spiel = $this->spiele[$spielId] ?? null;
            if ($spiel !== null && ($spiel->getTeamIdA() === $teamId || $spiel->getTeamIdB() === $teamId)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Hat jedes Team mindestens ein Spiel absolviert (dann Tabelle/Platzierung anzeigen)?
     */
    public function zeigeTabelle(): bool
    {
        return 0 < min(array_map(
            static fn(Turniertabellenzeile $zeile) => $zeile->spiele,
            $this->platzierung->turniertabelle,
        ));
    }

    /**
     * Sind alle Spiele gespielt und kein Penalty mehr offen?
     */
    public function istTurnierBeendet(): bool
    {
        if (!empty($this->platzierung->penaltys->ausstehend)) {
            return false;
        }
        $minSpiele = min(array_map(
            static fn(Turniertabellenzeile $zeile) => $zeile->spiele,
            $this->platzierung->turniertabelle,
        ));
        return ($this->getAnzahlTeams() - 1) === $minSpiele;
    }

    /**
     * Penaltyspalte anzeigen, wenn Penaltys vorgesehen oder falsch eingetragen sind.
     */
    public function zeigePenaltySpalte(): bool
    {
        if (!$this->validatePenaltyErgebnisse()) {
            return true;
        }
        return !empty($this->platzierung->penaltys->gesamt);
    }

    /**
     * Wurden Penaltyergebnisse nur bei dafür vorgesehenen Spielen eingetragen?
     */
    public function validatePenaltyErgebnisse(): bool
    {
        foreach ($this->spiele as $spielId => $spiel) {
            if (
                ($spiel->getPenaltyA() !== null || $spiel->getPenaltyB() !== null)
                && !in_array($spielId, $this->platzierung->penaltys->gesamt)
            ) {
                return false;
            }
        }
        return true;
    }

    /**
     * Wurde bei diesem Spiel ein Penalty eingetragen, obwohl keiner vorgesehen ist?
     */
    public function validatePenaltySpiel(SpielAnsicht $spiel): bool
    {
        return ($spiel->getPenaltyA() !== null || $spiel->getPenaltyB() !== null)
            && !$this->hatPenaltySpiel($spiel->getSpielId());
    }

    /**
     * HTML-String der ausstehenden Penaltybegegnungen ("Team A | Team B").
     */
    public function getPenaltyWarnung(): string
    {
        $begegnungen = [];
        foreach ($this->platzierung->penaltys->ausstehend as $spielId) {
            $spiel = $this->spiele[$spielId] ?? null;
            if ($spiel !== null) {
                $begegnungen[] = $spiel->getTeamnameA() . ' | ' . $spiel->getTeamnameB();
            }
        }
        return implode('<br>', $begegnungen);
    }

    /**
     * @return array<int, string> Team-ID => Trikotfarbe/-punkt; leer in der Spielplanphase
     */
    public function getTrikotColors(SpielAnsicht $spiel, bool $html = true): array
    {
        if ($this->turnier->isSpielplanPhase()) {
            return [];
        }
        return TrikotfarbenService::ermittle($spiel->getSpiel(), $html);
    }
}
