<?php

namespace App\Service\Turnier;

use Helper;
use App\Entity\Turnier\Turnier;

class TurnierFormService
{
    public static function isEditable(string $field, ?Turnier $turnier): bool
    {
        if (Helper::$ligacenter) {
            return true;
        }

        if (is_a($turnier, Turnier::class)) {

            if ($field == 'datum' && in_array($turnier->getPhase(), ['warte', 'setz', 'ergebnis', 'spielplan'])) {
                return false;
            }

            if ($field == 'startzeit' && in_array($turnier->getPhase(), ['ergebnis', 'spielplan'])) {
                return false;
            }

            if ($field == 'besprechung' && in_array($turnier->getPhase(), ['ergebnis', 'spielplan'])) {
                return false;
            }

            if ($field == 'startgebuehr' && in_array($turnier->getPhase(), ['setz', 'ergebnis', 'spielplan'])) {
                return false;
            }

            if ($field == 'sofort_oeffnen' && in_array($turnier->getPhase(), ['setz', 'ergebnis', 'spielplan'])) {
                return false;
            }

            if ($field == 'art_block' && in_array($turnier->getPhase(), ['warte', 'setz', 'ergebnis', 'spielplan'])) {
                return false;
            }

            if ($field == 'plaetze' && in_array($turnier->getPhase(), ['setz', 'ergebnis', 'spielplan'])) {
                return false;
            }

            if ($field == 'min_teams' && in_array($turnier->getPhase(), ['setz', 'ergebnis', 'spielplan'])) {
                return false;
            }

            if ($field == 'single_higher' && (!TurnierValidatorService::isErweiterbarBlockhoch($turnier) || TurnierService::isErweitertBlock($turnier))) {
                return false;
            }

            if ($field == 'single_lower' && (!TurnierValidatorService::isErweiterbarBlockrunter($turnier) || TurnierService::isErweitertBlock($turnier))) {
                return false;
            }

            if ($field == 'single_none' && TurnierService::isErweitertBlock($turnier)) {
                return false;
            }

            if ($field == 'multiple_free' && (!TurnierValidatorService::isErweiterbarBlockfrei($turnier) || TurnierService::isBlockfrei($turnier))) {
                return false;
            }

            if ($field == 'multiple_none' && TurnierService::isBlockfrei($turnier)) {
                return false;
            }
        }

        return true;
    }

    public static function isSelected(string $field, Turnier $turnier)
    {

        if ($field == 'single_higher' && $turnier->isBlockErweitertHoch()) {
            return true;
        }

        if ($field == 'single_lower' && $turnier->isBlockErweitertRunter()) {
            return true;
        }

        if ($field == 'single_none' && !$turnier->isBlockErweitertRunter() && !$turnier->isBlockErweitertHoch()) {
            return true;
        }

        if ($field == 'multiple_free' && $turnier->isBlockErweitertFrei()) {
            return true;
        }

        if ($field == 'multiple_none' && !$turnier->isBlockErweitertFrei()) {
            return true;
        }

        return false;
    }
}
