<?php
require_once '../../init.php';
Helper::$teamcenter_no_redirect = true; // Verhindert Weiterleitung, bei der Überprüfung, ob das Passwort geändert wurde
require_once '../../logic/session_team.logic.php'; //Auth

unset($_SESSION['logins']['team']);

Html::info("Logout erfolgreich");
header('Location: ../teamcenter/tc_login.php');
die();