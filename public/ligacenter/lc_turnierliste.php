<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

//Füge Links zum Weiterverarbeiten der ausgewählten Turniere hinzu; diese werden dem Teamplate übergeben

//Für Turniere die nicht in der Ergebnis-Phase sind:
$turniere_no_erg = Turnier::get_all_turniere("WHERE saison='".Config::SAISON."' AND phase != 'ergebnis'");
foreach ($turniere_no_erg as $turnier_id => $turnier){
    //Links
    $turniere_no_erg[$turnier_id]['link_zeile'] = "lc_team_anmelden.php?turnier_id=". $turnier_id;
    $turniere_no_erg[$turnier_id]['links'] = 
        array(
            Form::link("lc_team_anmelden.php?turnier_id=".$turnier_id, '<i class="material-icons">how_to_reg</i> An/abmelden'), 
            Form::link("../liga/turnier_details.php?turnier_id=".$turnier_id, '<i class="material-icons">info</i> Details'),
            Form::link("lc_turnier_bearbeiten.php?turnier_id=".$turnier_id, '<i class="material-icons">create</i> Turnier bearbeiten'),
            Form::link("lc_turnier_log.php?turnier_id=".$turnier_id, '<i class="material-icons">info_outline</i> Log einsehen'),
            Form::link("lc_spielplan_verwalten.php?turnier_id=".$turnier_id, '<i class="material-icons">playlist_play</i> Spielplan/Ergebnis bearbeiten')
        );
    if ($turnier['phase'] == 'spielplan'){
        array_push($turniere_no_erg[$turnier_id]['links'], Form::link("lc_spielplan.php?turnier_id=".$turnier_id, '<i class="material-icons">reorder</i> Spielergebnis eintragen'));
    }
}

//Für Turniere die in der Ergebnisphase sind:
$turniere_erg = Turnier::get_all_turniere("WHERE saison='".Config::SAISON."' AND phase = 'ergebnis'");
foreach ($turniere_erg as $turnier_id => $turnier){
  //Links
  $turniere_erg[$turnier_id]['link_zeile'] = "lc_team_anmelden.php?turnier_id=". $turnier_id;
  $turniere_erg[$turnier_id]['links'] = 
      array(
          Form::link("lc_team_anmelden.php?turnier_id=".$turnier_id, '<i class="material-icons">how_to_reg</i> An/abmelden'), 
          Form::link("../liga/turnier_details.php?turnier_id=".$turnier_id, '<i class="material-icons">info</i> Details'),
          Form::link("lc_turnier_bearbeiten.php?turnier_id=".$turnier_id, '<i class="material-icons">create</i> Turnier bearbeiten'),
          Form::link("lc_turnier_log.php?turnier_id=".$turnier_id, '<i class="material-icons">info_outline</i> Log einsehen'),
          Form::link("lc_spielplan_verwalten.php?turnier_id=".$turnier_id, '<i class="material-icons">playlist_play</i> Ergebnis verwalten'),
          Form::link("lc_spielplan.php?turnier_id=".$turnier_id, '<i class="material-icons">reorder</i> Spielergebnisse verändern')
      );
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
include '../../logic/turnierliste.logic.php'; //Als absolute Ausnahme zur Init
include '../../templates/turnierliste.tmp.php';
?>

<h2 class="w3-text-primary" id="ergebnis">Vergangene Turniere</h2>
<?php
//Turniere die in der Ergebnisphase sind:
$turniere = $turniere_erg;
include '../../logic/turnierliste.logic.php'; //Als absolute Ausnahme zur Init
include '../../templates/turnierliste.tmp.php';

include '../../templates/footer.tmp.php';
