<?php
// Dies hier muss in jeder geschützten Seite direkt nach init.php eingefügt werden!
use App\Repository\Team\TeamRepository;

if (!isset($_SESSION['logins']['team'])) {
  $_SESSION['tc_redirect'] = db::escape($_SERVER['REQUEST_URI']); //Damit man nach dem Login direkt auf die gewünschte Seite geführt wird
  Html::info("Du wirst nach deinem Login weitergeleitet.");
  header('Location: ../teamcenter/tc_login.php?redirect');
  die();
}

$team = new Team ($_SESSION['logins']['team']['id']);
$teamEntity = TeamRepository::get()->team($_SESSION['logins']['team']['id']);

if (!Helper::$teamcenter_no_redirect && $team->details['passwort_geaendert'] === 'Nein'){
  Html::info("Bitte ändere zuerst das von uns vergebene Passwort.");
  header('Location: tc_pw_aendern.php');
  die();
}

if (!Helper::$teamcenter_no_redirect && empty($team->details['ligavertreter'])){
  Html::info("Bitte tragt vor der Nutzung des Teamcenters erneut einen Ligavertreter ein, welcher unsere aktuellen "
      . Html::link(Nav::LINK_DSGVO,"Datenschutz-Hinweise", true, 'security')
      . " gelesen und akzeptiert hat. Beachtet bitte, dass jedes Team nur einen Ligavertreter haben kann.", esc:false);
  header('Location: tc_teamdaten_aendern.php');
  die();
}

Html::$titel = $_SESSION['logins']['team']['name'];
Helper::$teamcenter = true; // Dies zeigt allen Dateien (insbeondere .tmp.php), dass man sich im Teamcenter befindet.