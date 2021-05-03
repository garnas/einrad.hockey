<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

$heute = date("Y-m-d");

$turniere = Turnier::get_eigene_turniere($_SESSION['logins']['team']['id']);

if (empty($turniere)) {

    Html::notice('Dein Team richtet zurzeit kein Turnier aus - '
        . Html::link('tc_turnier_erstellen.php', 'Erstelle ein Turnier', icon: 'create')
        . 'um es verwalten zu können und Turniereinstellungen zu ändern.',
        esc: false);
    Helper::reload('/teamcenter/tc_start.php');

} // end if


foreach ($turniere as $turnier_id => $turnier) {
    //Links
    $turniere[$turnier_id]['links'] =
        [
            Html::link("tc_turnier_bearbeiten.php?turnier_id=" . $turnier_id, 'Turnier bearbeiten', icon: 'create'),
            Html::link("../liga/turnier_details.php?turnier_id=" . $turnier_id, 'Details', icon: 'info')
        ];
    if ($turnier['art'] === 'spass') {
        $turniere[$turnier_id]['links'][] = Html::link('../teamcenter/tc_spassturnier_anmeldung.php?turnier_id=' . $turnier['turnier_id'], '<i class="material-icons">how_to_reg</i> Teams manuell anmelden');
    }
    if ($turnier['phase'] === 'spielplan') {
        $turniere[$turnier_id]['links'][] = '<b>' . Html::link('../teamcenter/tc_spielplan.php?turnier_id=' . $turnier['turnier_id'], 'Ergebnisse eintragen', icon: 'reorder') . '</b>';
        $turniere[$turnier_id]['links'][] = '<b>' . Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], 'Turnierreport eintragen', icon: 'article') . '</b>';
        $turniere[$turnier_id]['row_color'] = 'w3-pale-yellow';
    }
    if ($turnier['phase'] === 'ergebnis') {
        $turniere[$turnier_id]['links'][] = Html::link('../teamcenter/tc_spielplan.php?turnier_id=' . $turnier['turnier_id'], 'Ergebnisse verändern', icon: 'reorder');
        $turniere[$turnier_id]['links'][] = Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier['turnier_id'], 'Turnierreport verändern', icon: 'article');
        $turniere[$turnier_id]['row_color'] = 'w3-pale-green';
    }
}

include '../../logic/turnierliste.logic.php'; //Auth

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

    <h1 class="w3-text-primary w3-center">Eigene Turniere verwalten</h1>

<?php

include '../../templates/turnierliste.tmp.php';
include '../../templates/footer.tmp.php';