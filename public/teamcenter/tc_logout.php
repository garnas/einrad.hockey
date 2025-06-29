<?php

use App\Service\Team\TeamService;

require_once '../../init.php';
Helper::$teamcenter_no_redirect = true; // Verhindert Weiterleitung, bei der Überprüfung, ob das Passwort geändert wurde
require_once '../../logic/session_team.logic.php'; //Auth

TeamService::remove_team_session();
Html::info("Logout erfolgreich");
Helper::reload("/teamcenter/tc_login.php");