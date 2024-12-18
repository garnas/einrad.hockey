<?php

namespace App\Service\Turnier;

use App\Entity\Turnier\Turnier;
use App\Service\Team\NLTeamService;
use App\Service\Team\TeamValidator;
use App\Entity\Team\nTeam;
use Html;
use Jenssegers\Date\Date;

class TurnierSnippets {

    public static function status(Turnier $turnier): string
    {
        if ($turnier->isCanceled()) {
            return '<span class="w3-text-red">abgesagt</span>';
        }
        if ($turnier->isSpielplanPhase()) {
            return '<span class="w3-text-gray">geschlossen</span>';
        }
        if ($turnier->isSpassTurnier()) {
            return '<span class="w3-text-grey">Nichtligaturnier</span>';
        }
        if (TurnierService::isLosen($turnier)) {
            return '<span class="w3-text-yellow">losen</span>';
        }
        if (!TurnierService::hasFreieSetzPlaetze($turnier)) {
            return '<span class="w3-text-red">voll</span>';
        }
        return '<span class="w3-text-green">frei</span>';
    }

    public static function rowColor(Turnier $turnier): string
    {
        if ($turnier->isCanceled()) {
            return 'w3-grey';
        }
        if ($turnier->isWartePhase()) {
            return 'w3-pale-yellow';
        }
        if ($turnier->isSetzPhase()) {
            return 'w3-pale-yellow';
        }
        if ($turnier->isSpielplanPhase()) {
            return 'w3-pale-blue';
        }
        if ($turnier->isErgebnisPhase()) {
            return 'w3-green';
        }
        return '';
    }


    public static function blockColor(Turnier $turnier, nTeam $team): string
    {
        if(
            TeamValidator::isValidRegularAnmeldung($team, $turnier, false)
        ) {
            return "<span class='w3-text-green'>" . $turnier->getBlock() . "</span>";
        }
        if (TeamValidator::isValidFreilos($team, $turnier, false)) {
            return "<span class='w3-text-yellow'>" . $turnier->getBlock() . "</span>";
        }
        return "<span class='w3-text-blue'>" . $turnier->getBlock() . "</span>";
    }

    public static function phase(Turnier $turnier): string
    {
        if ($turnier->isSpielplanPhase()) {
            $link = TurnierLinks::spielplan($turnier);
            $phase = self::translate($turnier->getPhase());
            Html::link($link, $phase, true);
        }

        if ($turnier->isSpassTurnier()) {
            return 'Nichtligaturnier';
        }

        if ($turnier->isFinalTurnier()) {
            return 'Finalturnier';
        }

        return (self::translate($turnier->getPhase()));
    }

    public static function plaetze(Turnier $turnier): string
    {
        return TurnierService::getAnzahlGesetzteTeams($turnier)
            . "(" . TurnierService::getAnzahlWartelisteTeams($turnier) . ")"
            . " von " . $turnier->getDetails()->getPlaetze();
    }

    public static function ortDatumBlock(Turnier $turnier, $html = true): string
    {
        $block = BlockService::toString(($html) ? $turnier : $turnier->getBlock());
        $block = $turnier->isCanceled() ? "(Abgesagt)" : $block;
        $datum = self::datum($turnier);
        $ort = $turnier->getDetails()->getOrt();
        return e($ort) . " am " . $datum . " " . $block;
    }

    public static function ortWochentagDatumBlock(Turnier $turnier): string
    {
        Date::setLocale('de');
        $date = Date::createFromTimestamp($turnier->getDatum()->getTimestamp());
        $datum = $date->format("D d.m.Y");

        $block = BlockService::toString($turnier);
        $ort = e($turnier->getDetails()->getOrt());
        return $ort . " am " . $datum . " " . $block;
    }

    public static function nameBrTitel(Turnier $turnier): string
    {
        if (!empty($turnier->getName())) {
            return e($turnier->getName()) . "<br>" . self::ortWochentagDatumBlock($turnier);
        }
        return self::ortWochentagDatumBlock($turnier);
    }

    public static function datum(Turnier $turnier): string
    {
        return $turnier->getDatum()->format("d.m.Y");
    }

    public static function wochentag(Turnier $turnier): string
    {
        Date::setLocale('de');
        return Date::createFromTimestamp($turnier->getDatum()->getTimestamp())->format("l");
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

    public static function getHandy(Turnier $turnier): string
    {
        $handy = e($turnier->getDetails()->getHandy());
        $handyNumbers = preg_replace('/[^0-9.]+/', '', $handy);
        return Html::link(
            'tel:' . str_replace(' ', '', $handyNumbers),
            "<i class='material-icons'>smartphone</i>" . $handy
        );
    }

    public static function getListen(Turnier $turnier): string
    {
        $warteliste = TurnierService::getWarteliste($turnier);
        $setzliste = TurnierService::getSetzListe($turnier);

        $html = '<p class="w3-text-grey w3-border-bottom w3-border-grey">Setzliste</p>';
        $html .= '<p>';
        if (TurnierService::getAnzahlGesetzteTeams($turnier) > 0) {
            $html .= '<i>';
            foreach ($setzliste as $anmeldung) {
                $teamname = e($anmeldung->getTeam()->getName());
                $block = BlockService::toString($anmeldung->getTeam());
                if ($anmeldung->getTeam()->id() === ($_SESSION['logins']['team']['id'] ?? 0)) {
                    $html .= "<span class='w3-text-green'><b>$teamname</b></span>";
                } else {
                    $html .= $teamname;
                }
                $html .= ' <span class="w3-text-primary">' . $block . '</span>';
                $html .= '<br>';
            }
            $html .= '</i>';
        } else {
            $html .= '<i>leer</i>';
        }
        $html .= '</p>';

        $html .= '<p class="w3-text-grey w3-border-bottom w3-border-grey">Warteliste</p>';
        $html .= '<p>';
        if (TurnierService::getAnzahlWartelisteTeams($turnier) > 0) {
            $html .= '<i>';
            foreach ($warteliste as $anmeldung) {
                $warteplatz = ($turnier->isSetzPhase()) ? $anmeldung->getPositionWarteliste() . ". " : "";
                $teamname = e($anmeldung->getTeam()->getName());
                $block = BlockService::toString($anmeldung->getTeam());
                if ($anmeldung->getTeam()->id() === ($_SESSION['logins']['team']['id'] ?? 0)) {
                    $html .= "<span class='w3-text-yellow'><b>" . $warteplatz . " " . $teamname . "</b></span>";
                } else {
                    $html .= $warteplatz . " " . $teamname;
                }
                $html .= ' <span class="w3-text-primary">' . $block . '</span>';
                $html .= '<br>';
            }
            $html .= '</i>';
        } else {
            $html .= '<i>leer</i>';
        }
        $html .= '</p>';

        $freiePlaetze = TurnierService::getFreieSetzPlaetze($turnier);
        $pleatze = $turnier->getDetails()->getPlaetze();
        if (NLTeamService::countNLTeams($turnier) > 0) {
            $html .= "<span class='w3-text-grey'>* Nichtligateam</span>";
        }
        $html .= "<p>Freie Plätze: $freiePlaetze von $pleatze</p>";

        if ($turnier->isSpassTurnier()) {
            $html .= '<p class="w3-text-green">Anmeldung erfolgt beim Ausrichter</p>';
        }

        return $html;
    }

}
