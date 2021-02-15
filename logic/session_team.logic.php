<?php
// Dies hier muss in jeder geschützten Seite direkt unterhalb von first.logic.php eingefügt werden!
if(!isset($_SESSION['team_id'])) {
  $_SESSION['tc_redirect'] = dbi::escape($_SERVER['REQUEST_URI']); //Damit man nach dem Login direkt auf die gewünschte Seite geführt wird
  Form::info("Du wirst nach deinem Login weitergeleitet.");
  header('Location: ../teamcenter/tc_login.php?redirect');
  die();
}

$team = new Team ($_SESSION['team_id']);

if (!Config::$teamcenter_no_redirect && $team->details['passwort_geaendert'] === 'Nein'){
  Form::info("Bitte ändere zuerst das von uns vergebene Passwort.");
  header('Location: tc_pw_aendern.php');
  die();
}

if (!Config::$teamcenter_no_redirect && empty($team->details['ligavertreter'])){
  Form::info("Bitte tragt vor der Nutzung des Teamcenters erneut einen Ligavertreter ein, welcher unsere aktuellen "
      . Form::link(Config::LINK_DSGVO,"Datenschutz-Hinweise", true, 'security')
      . " gelesen und akzeptiert hat. Beachtet bitte, dass jedes Team nur einen Ligavertreter haben kann.", esc:false);
  header('Location: tc_teamdaten_aendern.php');
  die();
}

Config::$titel = $_SESSION['teamname'];
Config::$teamcenter = true; // Dies zeigt allen Dateien (insbeondere .tmp.php) , das man sich im Teamcenter befindet.