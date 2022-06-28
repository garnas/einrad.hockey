<?php

namespace App\Service\Turnier;

use App\Entity\Turnier\Turnier;
use App\Service\Team\TeamValidator;
use Config;
use Feiertage;
use Helper;
use Html;
use db;

class TurnierValidatorService
{

    private Turnier $turnier;

    public function __construct(Turnier $turnier)
    {
        $this->turnier = $turnier;
    }

    public static function onCreate(Turnier $turnier): bool
    {
        $validator = new TurnierValidatorService($turnier);
        return $validator->hasValidArt()
            && $validator->hasValidAusrichter()
            && $validator->hasValidBlock()
            && $validator->hasValidDatum()
            && $validator->hasValidPlaetze()
            && $validator->hasValidUhrzeit();
    }

    public static function onChange(Turnier $turnier): bool
    {
        // Keine Änderungen in der Ergebnisphase
        if (
            $turnier->getPhase() === 'ergebnis'
            && !Helper::$ligacenter
        ) {
            Html::error("Turniere können in der Ergebnisphase nicht mehr geändert werden. Bitte wende dich unter "
                . Env::LAMAIL . " an den Ligaaussschuss.");
            return false;
        }

        $validator = new TurnierValidatorService($turnier);

        return $validator->mayChange()
            && $validator->hasValidArt()
            && $validator->hasValidAusrichter()
            && $validator->hasValidPhase()
            && $validator->hasValidUhrzeit();
    }

    public function mayChange(): bool
    {
        return $this->turnier->getPhase() != 'ergebnis' || Helper::$ligacenter;
    }

    public function hasValidPhase(): bool
    {
        if (!in_array($this->turnier->getPhase(), ['warte', 'setz', 'spielplan', 'ergebnis'], true)) {
            Html::error("Ungültige Phase");
            return false;
        }
        return true;
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

        if (TurnierService::getAnzahlGesetzteTeams($this->turnier) > $plaetze) {
            Html::error("Es sind mehr Teams angemeldet als Plätze vorhanden.");
            return false;
        }

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
        if ($this->turnier->isLigaturnier()) {
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

            if (time() > TurnierService::getTurnierEintrageFristUnix($this->turnier)) {
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

        if (
            Helper::$teamcenter
            && TeamValidator::isAmKalenderTagAufSetzliste($this->turnier, $ausrichter)
        ) {
            Html::error("Ihr befindet euch am Turniertag bereits auf einer Setzliste");
            return false;
        }

        if (
            !$ausrichter->isLigaTeam()
            || !$ausrichter->isAktiv()
        ) {
            Html::error("Ungültiger Ausrichter");
            return false;
        }

        return true;
    }

    /**
     * Ermittelt, ob ein Turnier nach oben erweiterbar ist
     *
     * @param Turnier $turnier
     * @return bool
     */
    public static function isErweiterbarBlockhoch(Turnier $turnier): bool
    {
        return $turnier->getPhase() === 'setz'
            && strlen($turnier->getBlock()) < 3
            && $turnier->getBlock() !== 'AB'
            && $turnier->getBlock() !== 'A'
            && $turnier->isLigaTurnier();
    }

    /**
     * Ermittelt, ob ein Turnier nach oben erweiterbar ist
     *
     * @param Turnier $turnier
     * @return bool
     */
    public static function isErweiterbarBlockfrei(Turnier $turnier): bool
    {
       return $turnier->getPhase() === 'setz'
             && $turnier->isLigaturnier()
             && $turnier->getBlock() != Config::BLOCK_ALL[0];
    }

}