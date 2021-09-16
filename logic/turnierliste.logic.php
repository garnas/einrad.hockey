<?php

// Füge Links zum Weiterverarbeiten der ausgewählten Turniere hinzu
// diese werden dem Teamplate übergeben
foreach ($db_turniere as $turnier){
    $turnier_id = $turnier->get_turnier_id();

    $turniere[$turnier_id]['datum'] = $turnier->get_datum();
    $turniere[$turnier_id]['ort'] = $turnier->get_ort();
    $turniere[$turnier_id]['tblock'] = $turnier->get_tblock();
    $turniere[$turnier_id]['tname'] = $turnier->get_tname();
    $turniere[$turnier_id]['phase'] = $turnier->get_phase();
    $turniere[$turnier_id]['teamname'] = Team::id_to_name($turnier->get_ausrichter());
    
    // Farbe des Turnierblocks festlegen
    $freilos = true;
    $turniere[$turnier_id]['block_color'] = 'w3-text-red';
    if ($turnier->is_spielberechtigt($_SESSION['logins']['team']['id'])){
        $turniere[$turnier_id]['block_color'] = 'w3-text-green';
        $freilos = false;
    }
    if ($freilos && $turnier->is_spielberechtig_freilos($_SESSION['logins']['team']['id']) && $anz_freilose > 0){
        $turniere[$turnier_id]['block_color'] = 'w3-text-yellow';
    }

    // Einfärben wenn schon angemeldet
    $turniere[$turnier_id]['row_color'] = '';
    if (isset($turnier_angemeldet[$turnier_id])){ // true -> Team ist angemeldet
        $liste = $turnier_angemeldet[$turnier_id]; // Erhalte Liste, auf der das Team gemeldet ist
        
        switch ($turnier_angemeldet[$turnier_id]) 
        {
            case 'spiele':
                $turniere[$turnier_id]['row_color'] = 'w3-pale-green';
                break;
            case 'melde':
                $turniere[$turnier_id]['row_color'] = 'w3-pale-yellow';
                break;
            case 'warte':
                $turniere[$turnier_id]['row_color'] = 'w3-pale-blue';
                break;
        }
    }

    // Einfärben der Angabe, ob das Turnier noch freie Plätze hat
    if ($turnier->get_phase() == 'spielplan') {
        $turniere[$turnier_id]['freivoll'] = '<span class="w3-text-gray">geschlossen</span>';
    } elseif ($turnier->get_freie_plaetze() > 0) {
        $turniere[$turnier_id]['freivoll'] = '<span class="w3-text-green">frei</span>';
    } elseif ($turnier->get_phase() == 'offen' && count($turnier->get_spielenliste()) + count($turnier->get_meldeliste()) > $turnier->get_plaetze()) {
        $turniere[$turnier_id]['freivoll'] = '<span class="w3-text-yellow">losen</span>';
    } elseif ($turnier->get_plaetze() - count($turnier->get_spielenliste()) <= 0) {
        $turniere[$turnier_id]['freivoll'] = '<span class="w3-text-red">voll</span>';
    }
}