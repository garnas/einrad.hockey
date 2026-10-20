<?php

require_once '../../init.php';
require_once '../../logic/session_sa.logic.php'; //Auth

unset($_SESSION['logins']['sa']);

Html::info("Logout erfolgreich");
Helper::reload('/liga/neues.php');
