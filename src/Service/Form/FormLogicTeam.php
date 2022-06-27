<?php

namespace App\Service\Form;

use App\Entity\Turnier\Turnier;
use App\Repository\DoctrineWrapper;
use App\Service\Team\NLTeamService;
use App\Service\Team\NLTeamValidator;
use App\Service\Turnier\TurnierService;
use Helper;
use Html;

class FormLogicTeam
{
    public static function nlTeamAnmelden(Turnier $turnier): void
    {
        $liste = $_POST['nl_liste'];
        $teamname = $_POST['nl_teamname'];
        $nlTeam = NLTeamService::findOrCreate($teamname);
        if (NLTeamValidator::isValidNLAnmeldung($nlTeam, $turnier, $liste)) {
            TurnierService::nlAnmelden($turnier, $nlTeam, $liste);
            DoctrineWrapper::manager()->persist($turnier);
            DoctrineWrapper::manager()->flush();
            Html::info("$teamname wurde angemeldet auf Liste: $liste");
            Helper::reload(get:'?turnier_id='. $turnier->id());
        } else {
            Html::error("Nichtligateam konnte nicht angemeldet werden.");
        }
    }
}