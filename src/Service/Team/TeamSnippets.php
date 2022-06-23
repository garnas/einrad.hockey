<?php

namespace App\Service\Team;

use App\Entity\Team\nTeam;
use Html;

class TeamSnippets
{
    public static function getEmailLink(nTeam $team): string
    {
        $emailsString = TeamService::getPublicEmailsAsString($team);
        return Html::mailto($emailsString, $team->getName());
    }

}