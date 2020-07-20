<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/la_session.logic.php'; //Auth

$anmeldungen = Turnier::get_all_anmeldungen();

//Füge Links zum Weiterverarbeiten der ausgewählten Turniere hinzu; diese werden dem Teamplate übergeben

//Für Turniere die nicht in der Ergebnis-Phase sind:
$turniere_no_erg = Turnier::get_all_turniere("WHERE saison='".Config::SAISON."' AND phase != 'ergebnis'");
foreach ($turniere_no_erg as $key => $turnier){
    $turniere_no_erg[$key]['link_anmelden'] = "lc_team_anmelden.php?turnier_id=". $turnier['turnier_id'];
    $turniere_no_erg[$key]['link_bearbeiten'] = "lc_turnier_bearbeiten.php?turnier_id=". $turnier['turnier_id'];
    $turniere_no_erg[$key]['link_log'] = "lc_turnier_log.php?turnier_id=". $turnier['turnier_id'];
    $turniere_no_erg[$key]['link_spielplan'] = "lc_spielplan_verwalten.php?turnier_id=". $turnier['turnier_id'];
    if (!in_array($turnier['art'],array('I','II','III'))){
        $turniere_no_erg[$key]['block_color'] = "w3-text-red";
        $turniere_no_erg[$key]['freivoll'] = "<span class='w3-text-red'>" . $turnier['art'] . "</span>";
    }else{
        $turniere_no_erg[$key]['freivoll'] = $turnier['art'];
    }
}

//Für Turniere die in der Ergebnisphase sind:
$turniere_erg = Turnier::get_all_turniere("WHERE saison='".Config::SAISON."' AND phase = 'ergebnis'");
foreach ($turniere_erg as $key => $turnier){
  $turniere_erg[$key]['link_anmelden'] = "lc_team_anmelden.php?turnier_id=". $turnier['turnier_id'];
  $turniere_erg[$key]['link_bearbeiten'] = "lc_turnier_bearbeiten.php?turnier_id=". $turnier['turnier_id'];
  $turniere_erg[$key]['link_log'] = "lc_turnier_log.php?turnier_id=". $turnier['turnier_id'];
  $turniere_erg[$key]['link_spielplan'] = "lc_spielplan_verwalten.php?turnier_id=". $turnier['turnier_id'];
  if (!in_array($turnier['art'],array('I','II','III'))){
      $turniere_erg[$key]['block_color'] = "w3-text-red";
      $turniere_erg[$key]['freivoll'] = "<span class='w3-text-red'>" . $turnier['art'] . "</span>";
  }else{
      $turniere_erg[$key]['freivoll'] = $turnier['art'];
  }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';?>

<h2 class="w3-text-grey">Turniere verwalten</h2>
<p>
  <a href='#ergebnis' class="no w3-hover-text-secondary w3-text-blue">Zu den Turnieren in der Ergebnisphase</a>
</p>

<h2 class="w3-text-primary">Anstehende Turniere</h2>
<?php 
//Turniere die nicht in der Ergebnis-Phase sind:
$turniere = $turniere_no_erg;
include '../../templates/turnierliste.tmp.php';
?>

<h2 class="w3-text-primary" id="ergebnis">Vergangene Turniere</h2>
<?php
//Turniere die in der Ergebnisphase sind:
$turniere = $turniere_erg;
include '../../templates/turnierliste.tmp.php';

include '../../templates/footer.tmp.php';