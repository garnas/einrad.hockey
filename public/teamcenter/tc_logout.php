<?php
// Pfade müssen eventuell angepasst werden
require_once '../../logic/first.logic.php'; //autoloader und Session
$no_redirect = true; //Verhindert die Endlosschleife, bei der Überprüfung, ob das Passwort geändert wurde
require_once '../../logic/session_team.logic.php'; //Auth

//In der Regel wird die gesammte Session beendet. Nur wenn man entweder aus dem LC oder aus dem TC ausloggen will, werden Variablen verändert
/* if (!isset($_SESSION['la_id'])){
    session_destroy();
    session_start();
}else{ */
    unset($_SESSION['team_id']); 
    unset($_SESSION['teamname']);
    unset($_SESSION['teamblock']);
//}

Form::info("Logout erfolgreich");
header('Location: ../teamcenter/tc_login.php');
die();