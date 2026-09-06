<?php

namespace App\Model\Spielplan;

use App\Entity\Turnier\Spiel;

/**
 * Anzeige-Sicht auf ein {@see Spiel} inklusive berechneter Anstoßzeit.
 *
 * Kapselt die früher direkt aus der DB gelesenen Spalten (teamname_a, zeit, …),
 * die von den Spielplan-Templates und der XML-Ausgabe genutzt werden.
 */
final class SpielAnsicht
{
    public function __construct(
        private readonly Spiel $spiel,
        private readonly string $zeit,
    ) {}

    public function getSpiel(): Spiel
    {
        return $this->spiel;
    }

    public function getSpielId(): int
    {
        return $this->spiel->getSpielId();
    }

    public function getZeit(): string
    {
        return $this->zeit;
    }

    public function getTeamIdA(): int
    {
        return $this->spiel->getTeamA()->id();
    }

    public function getTeamIdB(): int
    {
        return $this->spiel->getTeamB()->id();
    }

    public function getTeamnameA(): ?string
    {
        return $this->spiel->getTeamA()->getName();
    }

    public function getTeamnameB(): ?string
    {
        return $this->spiel->getTeamB()->getName();
    }

    public function getSchiriIdA(): int
    {
        return $this->spiel->getSchiriTeamA()->id();
    }

    public function getSchiriIdB(): int
    {
        return $this->spiel->getSchiriTeamB()->id();
    }

    public function getSchiriNameA(): ?string
    {
        return $this->spiel->getSchiriTeamA()->getName();
    }

    public function getSchiriNameB(): ?string
    {
        return $this->spiel->getSchiriTeamB()->getName();
    }

    public function getToreA(): ?int
    {
        return $this->spiel->getToreA();
    }

    public function getToreB(): ?int
    {
        return $this->spiel->getToreB();
    }

    public function getPenaltyA(): ?int
    {
        return $this->spiel->getPenaltyA();
    }

    public function getPenaltyB(): ?int
    {
        return $this->spiel->getPenaltyB();
    }
}
