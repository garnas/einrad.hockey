<?php
//Dies hier muss in jeder geschützten Seite direkt unterhalb von first.logic.php eingefügt werden!
if(!isset($_SESSION['la_id'])) {
  $redirect = db::escape($_SERVER['REQUEST_URI']); //Damit man nach dem Login direkt auf die gewünschte Seite geführt wird
  Form::affirm("Bitte zuerste einloggen");
  header('Location: ../ligacenter/lc_login.php?redirect=' . $redirect);
  die();
}
MailBot::warning_mail(); //Sendet eine Warnung, wenn Mails nicht versendet werden konnten.
$titel = 'Ligacenter';
$ligacenter = true; //Man kann sich gleichzeitig im Liga- und Teamcenter anmelden
$teamcenter = false; //Hiermit erkennt man, ob man sich gerade im Team- oder Ligacenter befindet, da Session-Variablen seitenübergreifend existieren