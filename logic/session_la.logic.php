<?php
// Dies hier muss in jeder geschützten Seite direkt unterhalb von first.logic.php eingefügt werden!
if(!isset($_SESSION['logins']['la'])) {
  $_SESSION['lc_redirect'] = dbi::escape($_SERVER['REQUEST_URI']); //Damit man nach dem Login direkt auf die gewünschte Seite geführt wird
  Form::info("Du wirst nach deinem Login weitergeleitet.");
  header('Location: ../ligacenter/lc_login.php?redirect');
  die();
}
MailBot::warning_mail(); // Sendet eine Warnung, wenn Mails nicht versendet werden konnten.

$titel = 'Ligacenter';

Config::$ligacenter = true; // Dies zeigt allen Dateien (insbeondere .tmp.php) , das man sich im Ligacenter befindet.