<?php

$anmeldungen = nTurnier::get_all_anmeldungen();
foreach ($turniere as $turnier_id => $turnier) {
    // Plätze frei?
    if ($turnier['plaetze'] > count($anmeldungen[$turnier['turnier_id']]['spiele'] ?? array())) {
        $turniere[$turnier_id]['freivoll'] = '<span class="w3-text-green">frei</span>';
    } else {
        $turniere[$turnier_id]['freivoll'] = '<span class="w3-text-red">voll</span>';
    }
    // Turnierart
    if (!in_array($turnier['art'], Config::TURNIER_ARTEN)) {
        $turniere[$turnier_id]['block_color'] = "w3-text-primary";
    }
}