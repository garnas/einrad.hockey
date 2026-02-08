<?php

namespace App\Service\Abstimmung;

use db;

use App\Service\Abstimmung\ConfigService;

class ValidatorService
{
    public static function validate(array $data): array
    {
        if (count($data) > 3) {
            return array("valid" => false, "message" => "Es wurden zu viele Stimmen vergeben.");
        }

        if (count($data) <= 0) {
            return array("valid" => false, "message" => "Es wurde keine Stimme vergeben.");
        }
        
        $valid_keys = array_keys(ConfigService::NAMES);
        $data_keys = array_keys($data);

        $invalid_keys = array_diff($data_keys, $valid_keys);
        if (count($invalid_keys) > 0) {
            return array("valid" => false, "message" => "Es wurde eine Stimme vergeben, die nicht existiert.");
        }

        return array("valid" => true, "message" => "Die Stimmenabgabe ist valide.");
    }
}