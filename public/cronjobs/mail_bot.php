<?php
/////////////////////////////////////////////////////////////////////////////
///////////////////////////////CRONJOB MAILBOT///////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

$_SESSION['logins']['cronjob'] = 'Cronjob';

MailBot::mail_bot();

//Meldungen protokollieren
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