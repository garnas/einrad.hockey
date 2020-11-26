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
    $turniere_no_erg[$turnier_id]['links'] = 
        array(
            Form::link("../liga/turnier_details.php?turnier_id=".$turnier_id, '<i class="material-icons">info</i> Details'),
            Form::link("lc_turnier_log.php?turnier_id=".$turnier_id, '<i class="material-icons">info_outline</i> Log einsehen'),
            Form::link("lc_team_anmelden.php?turnier_id=".$turnier_id, '<i class="material-icons">how_to_reg</i> Teams an/abmelden'), 
            Form::link("lc_turnier_bearbeiten.php?turnier_id=".$turnier_id, '<i class="material-icons">create</i> Turnier bearbeiten/löschen'),
            Form::link("lc_spielplan_verwalten.php?turnier_id=".$turnier_id, '<i class="material-icons">playlist_play</i> Spielplan/Ergebnis verwalten'),
            Form::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">article</i>Turnierreport bearbeiten')
        );
    if ($turnier['phase'] == 'spielplan'){
        array_push($turniere_no_erg[$turnier_id]['links'], Form::link("lc_spielplan.php?turnier_id=".$turnier_id, '<i class="material-icons">reorder</i> Spielergebnis eintragen'));
    }
}

//Für Turniere die in der Ergebnisphase sind:
$turniere_erg = Turnier::get_all_turniere("WHERE saison='".Config::SAISON."' AND phase = 'ergebnis'", "desc");
foreach ($turniere_erg as $turnier_id => $turnier){
  //Links
  $turniere_erg[$turnier_id]['links'] = 
      array(
            Form::link("../liga/turnier_details.php?turnier_id=".$turnier_id, '<i class="material-icons">info</i> Details'),
            Form::link("lc_turnier_log.php?turnier_id=".$turnier_id, '<i class="material-icons">info_outline</i> Log einsehen'),
            Form::link("lc_team_anmelden.php?turnier_id=".$turnier_id, '<i class="material-icons">how_to_reg</i> Teams an/abmelden'),  
            Form::link("lc_turnier_bearbeiten.php?turnier_id=".$turnier_id, '<i class="material-icons">create</i> Turnier bearbeiten'),
            Form::link("lc_spielplan_verwalten.php?turnier_id=".$turnier_id, '<i class="material-icons">playlist_play</i> Spielplan/Ergebnis verwalten'),
            Form::link("lc_spielplan.php?turnier_id=".$turnier_id, '<i class="material-icons">reorder</i> Spielergebnisse verändern'),
            Form::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">article</i>Turnierreport bearbeiten')
      );
}

//Gelöschte Turniere
$turniere_deleted = Turnier::get_deleted_turniere();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';?>

<h2 class="w3-text-grey">Turniere verwalten</h2>
<p>
    <?=Form::link('#ergebnis', 'Zu den Turnieren in der Ergebnisphase')?>
    <br>
    <?=Form::link('#deleted', 'Zu den Logs der gelöschten Turniere')?>
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
?>

<h2 class="w3-text-primary" id="deleted">Gelöschte Turniere</h2>
<p>
    <div class="w3-card w3-responsive">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th>Turnier</th>
                <th>Grund</th>
            </tr>
            <?php foreach ($turniere_deleted as $turnier){?>
                <tr>
                    <td style="white-space:nowrap;"><?=date("d.m.y", strtotime($turnier['datum']))?> in <?=$turnier['ort']?> (<?=$turnier['turnier_id']?>)</td>
                    <td style="white-space:nowrap;" class="w3-text-secondary"><?=$turnier['grund']?></td>
                </tr>
                <tr>
                    <td colspan='4'><?=Form::link('lc_turnier_log.php?turnier_id=' . $turnier['turnier_id'], 'Link zum Turnierlog')?></td>
                </tr>
            <?php }//end foreach?>
        </table>
    </div>
</p>
<?php
include '../../templates/footer.tmp.php';
