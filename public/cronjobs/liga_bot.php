<?php
/////////////////////////////////////////////////////////////////////////////
///////////////////////////////CRONJOB LIGABOT///////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

$_SESSION['logins']['cronjob'] = 'Cronjob';

LigaBot::liga_bot(); //TODO Helper::log verwenden

////Meldungen protokollieren
//foreach (($_SESSION['e_messages'] ?? array()) as $message){
//    echo 'Fehler: ' . $message . '<br><br>';
//}
//foreach (($_SESSION['w_messages'] ?? array()) as $message){
//    echo 'Hinweis: ' . $message . '<br><br>';
//}
//foreach (($_SESSION['a_messages'] ?? array()) as $message){
//    echo 'Info: ' . $message . '<br><br>';
//}
session_destroy();