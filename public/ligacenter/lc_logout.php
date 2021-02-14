<?php
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

/* if (!isset($_SESSION['team_id'])){
    session_destroy();
    session_start();
}else{*/
    unset($_SESSION['la_login_name']); 
    unset($_SESSION['la_id']);
//}

Form::info("Logout erfolgreich");
header('Location: ../liga/neues.php');
die();