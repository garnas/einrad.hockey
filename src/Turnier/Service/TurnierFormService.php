<?php

namespace App\Turnier\Service;

use App\Turnier\Entity\Turnier;

class TurnierFormService
{
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
