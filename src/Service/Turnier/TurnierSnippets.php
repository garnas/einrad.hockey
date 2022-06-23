<?php

namespace App\Service\Turnier;

use App\Entity\Turnier\Turnier;use App\Repository\Turnier\TurnierRepository;
use Html;
use Jenssegers\Date\Date;

class TurnierSnippets {

    public static function ortDatumBlock(Turnier $turnier): string
    {
        $block = BlockService::toString($turnier->getBlock());
        $datum = self::datum($turnier);
        $ort = $turnier->getDetails()->getOrt();
        return e($ort . " am " . $datum . " " . $block);
    }

    public static function ortWochentagDatumBlock(Turnier $turnier): string
    {
        Date::setLocale('de');
        $date = Date::createFromTimestamp($turnier->getDatum()->getTimestamp());
        $datum = $date->format("D d.m.Y");

        $block = BlockService::toString($turnier->getBlock());
        $ort = $turnier->getDetails()->getOrt();
        return e($ort . " am " . $datum . " " . $block);
    }

    public static function nameBrTitel(Turnier $turnier): string
    {
        if (!empty($turnier->getName())) {
            return e($turnier->getName() . " " . self::ortWochentagDatumBlock($turnier));
        }
        return self::ortWochentagDatumBlock($turnier);
    }

    public static function datum(Turnier $turnier): string
    {
        return $turnier->getDatum()->format("d.m.Y");
    }

    public static function translate(string $begriff): string
    {
        return match($begriff) {
            "warteliste" => "Warteliste",
            "setzliste" => "Setzliste",
            "warte" => "Wartephase",
            "setz" => "Setzphase",
            "spielplan" => "Spielplanphase",
            "ergebnis" => "Ergebnisphase",
            "I" => "Blockeigenes Turnier (I)",
            "II" => "Blockhöheres Turnier (II)",
            "final" => "Abschlussturnier",
            "spass" => "Nichtligaturnier",
            default => e($begriff)
        };
    }

    public static function getHandy(Turnier $turnier)
    {
        $handy = e($turnier->getDetails()->getHandy());
        $handyNumbers = preg_replace('/[^0-9.]+/', '', $handy);
        return Html::link(
            'tel:' . str_replace(' ', '', $handyNumbers),
            "<i class='material-icons'>smartphone</i>" . $handy
        );
    }

    public static function getListen(Turnier $turnier)
    {
        $warteliste = TurnierService::getWarteliste($turnier);
        $setzliste = TurnierService::getSetzListe($turnier);

        $html = '<p class="w3-text-grey w3-border-bottom w3-border-grey">Warteliste</p>';
        $html .= '<p>';
        if (TurnierService::getAnzahlWartelisteTeams($turnier) > 0) {
            $html .= '<i>';
            foreach ($warteliste as $anmeldung) {
                $warteplatz = ($turnier->isSetzPhase()) ? $anmeldung->getPositionWarteliste() . ". " : "";
                $teamname = e($anmeldung->getTeam()->getName());
                $block = BlockService::toString($anmeldung->getTeam()->getBlock());
                $html .= $warteplatz . " " . $teamname;
                $html .= '<span class="w3-text-primary">' . $block . '</span>';
                $html .= '<br>';
            }
            $html .= '</i>';
        } else {
            $html .= '<i>leer</i>';
        }
        $html .= '</p>';

        $html = '<p class="w3-text-grey w3-border-bottom w3-border-grey">Setzliste</p>';
        $html .= '<p>';
        if (TurnierService::getAnzahlGesetzteTeams($turnier) > 0) {
            $html .= '<i>';
            foreach ($setzliste as $anmeldung) {
                $teamname = e($anmeldung->getTeam()->getName());
                $block = BlockService::toString($anmeldung->getTeam()->getBlock());
                $html .= $teamname;
                $html .= '<span class="w3-text-primary">' . $block . '</span>';
                $html .= '<br>';
            }
            $html .= '</i>';
        } else {
            $html .= '<i>leer</i>';
        }
        $html .= '</p>';

        $freiePlaetze = TurnierService::getFreieSetzPlaetze($turnier);
        $pleatze = $turnier->getDetails()->getPlaetze();
        $html .= "<p>Freie Plätze: $freiePlaetze von $pleatze</p>";

        if ($turnier->isSpassTurnier()) {
            $html .= '<p class="w3-text-green">Anmeldung erfolgt beim Ausrichter</p>';
        }

        return $html;
    }

}
