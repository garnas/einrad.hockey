<?php
require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

unset($_SESSION['logins']['la']);

Html::info("Logout erfolgreich");
Helper::reload('/liga/neues.php');