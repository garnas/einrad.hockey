<?php
require_once '../../init.php';
require_once '../../logic/session_oa.logic.php'; //Auth

unset($_SESSION['logins']['oa']);

Html::info("Logout erfolgreich");
Helper::reload('/liga/neues.php');