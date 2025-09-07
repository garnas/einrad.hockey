<?php

namespace App\Service\Team;

use App\Entity\Team\Freilos;
use App\Entity\Team\nTeam;
use App\Entity\Team\Spieler;
use App\Service\Turnier\TurnierSnippets;
use Html;

class TeamSnippets
{
    public static function getEmailLink(nTeam $team): string
    {
        $emailsString = TeamService::getPublicEmailsAsString($team);
        return Html::mailto($emailsString, $team->getName());
    }

    /**
     * @param nTeam $team
     * @return string
     */
    public static function freilose(nTeam $team): string
    {
        $freilose = $team->getGueltigeFreilose()->toArray();
        usort($freilose, static function(Freilos $a, Freilos $b) {
            if ($a->getSaison() == $b->getSaison()) {
                return $b->getErstelltAm() <=> $a->getErstelltAm();
            }
            return $b->getSaison() <=> $a->getSaison();
        });
        $html = '<ul class="w3-ul w3-leftbar w3-border-tertiary">';

        /** @var Freilos $freilos */
        foreach ($freilose as $freilos) {
            $html .= "<li>";
            $html .= ($freilos->isGesetzt()) ? "<s>" : "<span class='w3-text-green'>";
            $html .= $freilos->getGrund()->value;
            $html .= " (Saison " . Html::get_saison_string($freilos->getSaison()) . ")";
            $html .= ($freilos->isGesetzt()) ? "</s>" : "</span>";
            $html .= "<br><span class='w3-text-grey'>Erhalten am " . $freilos->getErstelltAm()->format("d.m.Y") . "</span>";

            if ($freilos->getTurnierAusgerichtet()) {
                $html .= "<br><span class='w3-text-grey'>(";
                $html .= TurnierSnippets::ortDatumBlock($freilos->getTurnierAusgerichtet());
                $html .= ")</span>";
            }
            if ($freilos->getVorherigesFreilos()) {
                $html .= "<br><span class='w3-text-grey'>(";
                $html .=  TurnierSnippets::ortDatumBlock($freilos->getVorherigesFreilos()->getTurnier());
                $html .= ")</span>";
            }
            if ($freilos->isGesetzt()) {
                $html .= "<span class='w3-text-grey'>";
                $html .= "<br>Gesetzt am " . $freilos->getGesetztAm()->format("d.m.Y");
                $turnier = $freilos->getTurnier();
                $html .= " für " .TurnierSnippets::ortDatumBlock(turnier: $turnier, html: false);
                $html .= "</span>";
            }
            if ($freilos->isGesetzt() && FreilosService::validateFreilosRecycling($freilos)) {
                $html .= "<br><span class='w3-text-green'>Nach dem Turnier erhaltet ihr ein neues Freilos.</span>";
            }

            $html .= "</li>";
        }
        $html .= '</ul>';
        return $html;
    }

    public static function schiritag(Spieler $spieler): string
    {
        if (empty($spieler->getSchiri())) {
            return '';
        }
        $saison_text = Html::get_saison_string($spieler->getSchiri());
        $junior = ($spieler->isJunior()) ? "<i class='w3-text-grey'>junior</i>" : "";
        $ausbilder = (SpielerService::isAusbilder($spieler)) ? "<i class='w3-text-grey'>Ausbilder/in</i>" : "";
        if (SpielerService::isSchiri($spieler)) {
            $icon = Html::icon("check_circle_outline");
            return "<span class='w3-text-green'>$icon $saison_text $junior $ausbilder</span>";
        } else {
            $icon = Html::icon("block");
            return "<span class='w3-text-grey'><s>$icon $saison_text $junior</s> $ausbilder</span>";
        }
    }


}