<?php
$turniere[$turnier_id]['datum'] = $turnier->get_datum();
$turniere[$turnier_id]['ort'] = $turnier->get_ort();
$turniere[$turnier_id]['tblock'] = $turnier->get_tblock();
$turniere[$turnier_id]['tname'] = $turnier->get_tname();
$turniere[$turnier_id]['phase'] = $turnier->get_phase();
$turniere[$turnier_id]['teamname'] = Team::id_to_name($turnier->get_ausrichter());
$turniere[$turnier_id]['freivoll'] = $turnier->get_freie_plaetze_status();

// Farbe des Turnierblocks festlegen
$freilos = true;
$turniere[$turnier_id]['block_color'] = 'w3-text-red';
if ($turnier->is_spielberechtigt($_SESSION['logins']['team']['id'])) {
    $turniere[$turnier_id]['block_color'] = 'w3-text-green';
    $freilos = false;
}
if ($freilos && $turnier->is_spielberechtig_freilos($_SESSION['logins']['team']['id']) && $team_anz_freilose > 0) {
    $turniere[$turnier_id]['block_color'] = 'w3-text-yellow';
}

// Einfärben wenn schon angemeldet
switch ($team_turniere_angemeldet[$turnier_id] ?? 'kein') {
    case 'spiele':
        $turniere[$turnier_id]['row_color'] = 'w3-pale-green';
        break;
    case 'melde':
        $turniere[$turnier_id]['row_color'] = 'w3-pale-yellow';
        break;
    case 'warte':
        $turniere[$turnier_id]['row_color'] = 'w3-pale-blue';
        break;
    case 'kein':
        $turniere[$turnier_id]['row_color'] = '';
        break;
}
