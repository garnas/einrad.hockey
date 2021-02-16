<?php
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

unset($_SESSION['logins']['la']);

Form::info("Logout erfolgreich");
header('Location: ../liga/neues.php');
die();