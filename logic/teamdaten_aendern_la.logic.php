<?php
if(isset($_POST['change_la'])) {
    $error=false;
    
    $team_id = $akt_team->team_id;
    $neuer_teamname=$_POST['teamname'];
    $freilose=$_POST['freilose'];
    $passwort=$_POST['passwort'];

    if($neuer_teamname == $daten['teamname'] && $passwort == 'Neues Passwort vergeben' && $freilose == $daten['freilose']) {
      Form::error("Es wurden keine Daten verändert");
      $error = true;
    }
    if(!empty(Team::teamname_to_teamid($neuer_teamname)) && $neuer_teamname != htmlspecialchars_decode($daten['teamname'])) {
      Form::error("Der Teamname existiert bereits.");
      $error = true;
    }
    if(empty($neuer_teamname) or $freilose < 0 or empty($passwort)) {
      Form::error( "Felder dürfen nicht leer sein");
      $error = true;
    }
    
  if(!$error) {
    if ($neuer_teamname != htmlspecialchars_decode($daten['teamname'])){
      $akt_team->set_teamname($neuer_teamname);
      Form::affirm("Der Teamname wurde geändert");
      $change = true;
    }
    if ($freilose != $daten['freilose']){
      $akt_team->set_freilose($freilose);
      Form::affirm("Anzahl der Freilose wurde geändert");
      $change = true;
    }
    if ($passwort != 'Neues Passwort vergeben'){
      $akt_team->set_passwort($passwort, 'Nein');
      Form::affirm("Passwort wurde geändert");
      $change = true;
    }
  }

  if ($change ?? false) {
    header('Location: lc_teamdaten.php?team_id=' . $team_id);
    die();
  }
}
