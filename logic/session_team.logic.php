<?php
//Dies hier muss in jeder geschützten Seite direkt unterhalb von first.logic.php eingefügt werden!
if(!isset($_SESSION['team_id'])) {
  $_SESSION['tc_redirect'] = db::escape($_SERVER['REQUEST_URI']); //Damit man nach dem Login direkt auf die gewünschte Seite geführt wird
  Form::affirm("Du wirst nach deinem Login weitergeleitet.");
  header('Location: ../teamcenter/tc_login.php?redirect');
  die();
}

$akt_team = new Team ($_SESSION['team_id']);
$daten = $akt_team->get_details();

if (!isset($no_redirect) && $daten['passwort_geaendert'] == 'Nein'){
  Form::affirm("Bitte ändere zuerst das von uns vergebene Passwort");
  header('Location: tc_pw_aendern.php');
  die();
}

if (!isset($no_redirect) && $daten['ligavertreter'] == ''){
  Form::affirm("Bitte tragt vor der Nutzung des Teamcenters erneut einen Ligavertreter ein, welcher unsere aktualisierten " . Form::link(Config::LINK_DSGVO,"Datenschutz-Hinweise") . " gelesen und akzeptiert hat. Beachtet bitte, dass jedes Team in der Saison " . Form::get_saison_string() . " nur noch einen Ligavertreter haben kann.");
  header('Location: tc_teamdaten_aendern.php');
  die();
}

$titel = $_SESSION['teamname'];
$ligacenter = false; //Man kann sich gleichzeitig im Liga- und Teamcenter anmelden
$teamcenter = true; //Hiermit erkennt man, ob man sich gerade im Team- oder Ligacenter befindet, da Session-Variablen seitenübergreifend existieren