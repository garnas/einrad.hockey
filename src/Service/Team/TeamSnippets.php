<?php

namespace App\Service\Team;

use App\Entity\Team\nTeam;
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
        $freilose = $team->getGueltigeFreilose();
        $html = '<ul class="w3-ul w3-leftbar w3-border-tertiary">';
        foreach ($freilose as $freilos) {
            $html .= "<li> ";
            $html .= $freilos->getGrund()->value . " (Saison " . Html::get_saison_string($freilos->getSaison()) . ")";
            if ($freilos->isGesetzt()) {
                $html .= "<br>Gesetzt am: " . $freilos->getGesetztAm()->format("d.m.Y");
                $turnier = $freilos->getTurnier();
                $html .= TurnierSnippets::ortDatumBlock(turnier: $turnier, html: false);
            }
            $html .= "</li>";
        }
        $html .= '</ul>';
        return $html;
    }

}