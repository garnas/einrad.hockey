<?php

namespace abstimmung_la_2026\Abstimmung;

use App\Entity\Team\nTeam;
use env;

class VotingService
{
    /**
     * Verschlüsselung der Team-ID
     *
     * @param nTeam $team
     * @return string
     */
    public static function teamIdToHash(nTeam $team): string
    {
        return hash_hmac('sha256', $team->id(), Env::ABSTIMMUNG_KEY);
    }

}
