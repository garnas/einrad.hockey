<?php

use App\Repository\Team\TeamRepository;
use App\Service\Team\TeamService;

if (isset($_POST['la_team_waehlen'])) {
    $teamname = $_POST['la_team_waehlen'];
    $team  = TeamRepository::get()->findByName($teamname);
    if ($team?->isLigaTeam()) {
        Helper::reload(get: "?team_id={$team->id()}");
    }

    Html::error("Team wurde nicht gefunden oder ist kein aktives Ligateam.");
    Helper::reload();
}
