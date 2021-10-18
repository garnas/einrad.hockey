<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

//Füge Links zum Weiterverarbeiten der ausgewählten Turniere hinzu; diese werden dem Teamplate übergeben

//Für Turniere die nicht in der Ergebnis-Phase sind:
$turniere_no_erg = Turnier::get_turniere('ergebnis', false)
    + Turnier::get_turniere('ergebnis', false, saison:Config::SAISON + 1);

foreach ($turniere_no_erg as $turnier_id => $turnier){
    //Links
    $turniere_no_erg[$turnier_id]['links'] = 
        [
            Html::link("../liga/turnier_details.php?turnier_id=".$turnier_id, '<i class="material-icons">info</i> Details'),
            Html::link("lc_turnier_log.php?turnier_id=".$turnier_id, '<i class="material-icons">info_outline</i> Log einsehen'),
            Html::link("lc_team_anmelden.php?turnier_id=".$turnier_id, '<i class="material-icons">how_to_reg</i> Teams an/abmelden'),
            Html::link("lc_turnier_bearbeiten.php?turnier_id=".$turnier_id, '<i class="material-icons">create</i> Turnier bearbeiten/löschen'),
            Html::link("lc_spielplan_verwalten.php?turnier_id=".$turnier_id, '<i class="material-icons">playlist_play</i> Spielplan/Ergebnis verwalten'),
            Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">article</i>Turnierreport bearbeiten')
        ];
    if ($turnier['phase'] == 'spielplan'){
        $turniere_no_erg[$turnier_id]['links'][] = Html::link("lc_spielplan.php?turnier_id=" . $turnier_id, '<i class="material-icons">reorder</i> Spielergebnis eintragen');
    }
}

//Für Turniere die in der Ergebnisphase sind:
$turniere_erg = Turnier::get_turniere('ergebnis', true, false);
foreach ($turniere_erg as $turnier_id => $turnier){
  //Links
  $turniere_erg[$turnier_id]['links'] = 
      array(
            Html::link("../liga/turnier_details.php?turnier_id=".$turnier_id, '<i class="material-icons">info</i> Details'),
            Html::link("lc_turnier_log.php?turnier_id=".$turnier_id, '<i class="material-icons">info_outline</i> Log einsehen'),
            Html::link("lc_team_anmelden.php?turnier_id=".$turnier_id, '<i class="material-icons">how_to_reg</i> Teams an/abmelden'),
            Html::link("lc_turnier_bearbeiten.php?turnier_id=".$turnier_id, '<i class="material-icons">create</i> Turnier bearbeiten'),
            Html::link("lc_spielplan_verwalten.php?turnier_id=".$turnier_id, '<i class="material-icons">playlist_play</i> Spielplan/Ergebnis verwalten'),
            Html::link("lc_spielplan.php?turnier_id=".$turnier_id, '<i class="material-icons">reorder</i> Spielergebnisse verändern'),
            Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">article</i>Turnierreport bearbeiten')
      );
}

//Gelöschte Turniere
$turniere_deleted = Turnier::get_deleted_turniere();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';?>

<h1 class="w3-text-grey">Turniere verwalten</h1>
<p> 
    <?=Html::link('lc_turnier_logs.php', '<span class="material-icons">info_outline</span> Gesamtlog anzeigen')?>
    <br>
    <?=Html::link('#anstehend', '<span class="material-icons">sports_hockey</span> Anstehende Turniere')?>
    <br>
    <?=Html::link('#ergebnis', '<span class="material-icons">emoji_events</span> Ergebnisphase-Turniere')?>
    <br>
    <?=Html::link('#deleted', '<span class="material-icons">not_interested</span> Gelöschte Turniere')?>
</p>

<h2 id="anstehend" class="w3-text-primary"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">sports_hockey</i> Anstehende Turniere</h2>
<?php
//Turniere die nicht in der Ergebnis-Phase sind:
$turniere = $turniere_no_erg;
include '../../logic/turnierliste.logic.php'; //Als absolute Ausnahme zur Init
include '../../templates/turnierliste.tmp.php';
?>

<h2 class="w3-text-primary" id="ergebnis"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">emoji_events</i> Ergebnisphase-Turniere</h2>
<?php
//Turniere die in der Ergebnisphase sind:
$turniere = $turniere_erg;
include '../../logic/turnierliste.logic.php'; //Als absolute Ausnahme zur Init
include '../../templates/turnierliste.tmp.php';
?>

<h2 class="w3-text-primary" id="deleted"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">not_interested</i> Gelöschte Turniere</h2>
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
                    <td colspan='4'><?=Html::link('lc_turnier_log.php?turnier_id=' . $turnier['turnier_id'], 'Link zum Turnierlog')?></td>
                </tr>
            <?php }//end foreach?>
        </table>
    </div>
</p>
<?php
include '../../templates/footer.tmp.php';
