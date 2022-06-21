<?php

namespace App\Service\Turnier;

use App\Entity\Turnier\Turnier;
use App\Service\Team\TeamService;
use Html;
use Config;
use Helper;
use Feiertage;

class TurnierValidatorService
{

    private Turnier $turnier;

    public function __construct(Turnier $turnier)
    {
        $this->turnier = $turnier;
    }

    public function isValid(): bool
    {
       return $this->hasValidArt()
            && $this->hasValidAusrichter()
            && $this->hasValidBlock()
            && $this->hasValidDatum()
            && $this->hasValidPlaetze()
            && $this->hasValidUhrzeit();
    }

    public function hasValidArt(): bool
    {

        if (
            Helper::$teamcenter
            &&
            in_array($this->turnier->getArt(), ['I', 'II', 'spass'], true)
        ) {
            return true;
        }

        if (
            Helper::$ligacenter
            &&
            in_array($this->turnier->getArt(), ['I', 'II', 'spass', 'final', 'fixed'], true)
        ) {
            return true;
        }

        Html::error("Ungültige Turnierart");
        return false;
    }

    public function hasValidBlock(): bool
    {
        $ausrichterBlock = $this->turnier->getAusrichter()->getBlock();
        $turnierBlock = $this->turnier->getBlock();
        $art = $this->turnier->getArt();

        switch ($art) {

            case ('I'):
                $valid = ($turnierBlock == $ausrichterBlock);
                break;

            case('II'):
                $higherBlocks = BlockService::getHigherBlocks($ausrichterBlock);
                $valid = in_array($turnierBlock, $higherBlocks, true);
                break;

            default:
                $valid = (in_array($turnierBlock, Config::BLOCK_ALL, true) || $turnierBlock === null);
                break;

        }

        if (!$valid) {
            Html::error("Ausrichterblock ($ausrichterBlock), Turnierblock ($turnierBlock) und Turnierart ($art) passen nicht zusammen");
        }

        return $valid;
    }

    public function hasValidPlaetze(): bool
    {

        $plaetze = $this->turnier->getDetails()->getPlaetze();

        if (
            Helper::$teamcenter
            && $plaetze >= 5
            && $plaetze <= 5
        ) {
            return true;
        }

        if (
            Helper::$ligacenter
            && $plaetze >= 0
            && $plaetze <= 12
        ) {
            return true;
        }

        Html::error("Ungültige Anzahl an Plätzen ($plaetze Plätze)");
        return false;

    }

    public function hasValidDatum(): bool
    {
        if (Helper::$ligacenter) {
            return true;
        }

        $unixTime = $this->turnier->getDatum()->getTimestamp();
        if (TurnierService::isLigaTurnier($this->turnier)) {
            if (
                $unixTime < strtotime(Config::SAISON_ANFANG)
                || ($unixTime > strtotime(Config::SAISON_ENDE)
                )
            ) {
                Html::error("Das Datum liegt außerhalb der Saison.");
                return false;
            }

            $feiertage = Feiertage::finden(date("Y", $unixTime));
            if (Helper::$teamcenter && !in_array($unixTime, $feiertage) && date('N', $unixTime) < 6) {
                Html::error("Das Datum liegt nicht am Wochende und ist kein bundesweiter Feiertag.");
                return false;
            }

            if (time() > TurnierService::getTurnierEintrageFrist($this->turnier)) {
                Html::error("Turniere können nur vier Wochen vor dem Spieltag eingetragen werden");
                return false;
            }
        }

        return true;
    }

    public function hasValidUhrzeit(): bool
    {

        if(Helper::$ligacenter) {
            return true;
        }

        $stunde = (int) $this->turnier->getDetails()->getStartzeit()->format('G');

        // Validierung Startzeit:
        if ($stunde < 9 || $stunde > 15) {
            Html::error("Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen spätestens"
                . " um 20:00&nbsp;Uhr beendet sein. Wende dich an den Ligaausschuss für spezielle"
                . " Spielzeiten.");
            return false;
        }

        return true;

    }

    public function hasValidAusrichter(): bool
    {
        $ausrichter = $this->turnier->getAusrichter();
        $datum = $this->turnier->getDatum();
        if (TeamService::isAmKalenderTagAufSetzliste($datum, $ausrichter)) {
            Html::error("Der Ausrichter befindet sich am Turniertag bereits auf einer Setzliste");
            return false;
        }
        return ($ausrichter->isLigaTeam() && $ausrichter->isAktiv());
    }

}