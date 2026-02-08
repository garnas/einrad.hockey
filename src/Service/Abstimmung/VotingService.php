<?php

namespace App\Service\Abstimmung;
use App\Service\Abstimmung\ConfigService;
use App\Entity\Team\nTeam;

class VotingService
{
    
    /**
     * Verschlüsselung der TeamID
     *
     * @param string $passkey
     * @return string
     */
    public static function teamid_to_hash(nTeam $team): string
    {
        return hash_hmac('sha256', $team->id(), ConfigService::KEY);
    }

}