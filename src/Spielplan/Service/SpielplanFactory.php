<?php

namespace App\Spielplan\Service;

use App\Spielplan\Entity\SpielplanDetails;
use App\Turnier\Entity\Spiel;
use App\Turnier\Entity\Turnier;
use App\Spielplan\Model\SpielAnsicht;
use App\Spielplan\Model\Spielplan;
use App\Spielplan\Repository\SpielplanRepository;
use App\Turnier\Service\TurnierService;

/**
 * Baut das {@see Spielplan}-Anzeigeaggregat für ein Turnier zusammen
 * (ersetzt `new Spielplan_JgJ($turnier)`).
 */
final class SpielplanFactory
{
    public static function ausTurnier(Turnier $turnier): Spielplan
    {
        $spielEntities = SpielplanRepository::get()->getSpiele($turnier);
        $details = SpielplanService::ermittleVorlage($turnier);
        $teamliste = TurnierService::getSpielenliste($turnier);

        if ($details === null) {
            trigger_error(
                "Spielplan konnte nicht ermittelt werden. (Turnier-ID " . $turnier->id() . ")",
                \E_USER_ERROR,
            );
        }

        self::pruefeTeams($turnier, $spielEntities, $teamliste);

        $zeiten = self::berechneZeiten($spielEntities, $details, $turnier);
        $spiele = [];
        foreach ($spielEntities as $spielId => $spiel) {
            $spiele[$spielId] = new SpielAnsicht($spiel, $zeiten[$spielId]);
        }

        $platzierung = PlatzierungService::berechne($spielEntities, $teamliste);
        LigapunkteService::anwenden($platzierung, $details, $teamliste);

        return new Spielplan(
            turnier: $turnier,
            details: $details,
            teamliste: $teamliste,
            spiele: $spiele,
            platzierung: $platzierung,
            pausen: $details->getPausenMap(),
        );
    }

    /**
     * @param array<int, Spiel> $spiele
     * @param array<int, array> $teamliste
     */
    private static function pruefeTeams(Turnier $turnier, array $spiele, array $teamliste): void
    {
        foreach ($spiele as $spiel) {
            if (
                !array_key_exists($spiel->getTeamA()->id(), $teamliste)
                || !array_key_exists($spiel->getTeamB()->id(), $teamliste)
            ) {
                trigger_error(
                    "Teams und Spielplan passen nicht zusammen. (Turnier-ID " . $turnier->id() . ")",
                    \E_USER_ERROR,
                );
            }
        }
    }

    /**
     * @param array<int, Spiel> $spiele keyed by Spiel-ID
     * @return array<int, string> Spiel-ID => "H:i"
     */
    private static function berechneZeiten(array $spiele, SpielplanDetails $details, Turnier $turnier): array
    {
        $spielzeit = (
            $details->getAnzahlHalbzeiten() * $details->getHalbzeitLaenge() + $details->getPuffer()
        ) * 60; // Sekunden

        $startzeit = $turnier->getDetails()->getStartzeit()->getTimestamp();
        $pausen = $details->getPausenMap();

        $zeiten = [];
        foreach (array_keys($spiele) as $spielId) {
            $zeiten[$spielId] = date("H:i", $startzeit);
            $startzeit += $spielzeit + ($pausen[$spielId] ?? 0) * 60;
        }
        return $zeiten;
    }
}
