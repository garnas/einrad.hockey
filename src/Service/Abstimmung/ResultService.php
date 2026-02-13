<?php

namespace App\Service\Abstimmung;

use App\Repository\Abstimmung\AbstimmungRepository;


class ResultService
{
    public static function getResult(): array
    {
        $abstimmung = AbstimmungRepository::get();
        $votes = $abstimmung->getAllVotes();
        
        $result = [];
        foreach (array_keys(ConfigService::NAMES) as $key) {
            $result[$key] = 0;
        }

        foreach ($votes as $vote) {
            $stimme = $vote['stimme'];
            $data = json_decode($stimme);
            foreach ($data as $key) {
                $result[$key] += 1;
            }
        }

        return $result;
    }

    public static function getParticipation() {
        $abstimmung = AbstimmungRepository::get();
        return count($abstimmung->getParticipation());
    }

    public static function getPercentString(int $value, int $base): string {
        if ($base == 0) {
            return "0 %";
        }

        $percent = round($value / $base, 4) * 100;
        return number_format($percent, 2, ",", ".") . " %";
    }
}