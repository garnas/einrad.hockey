<?php

//  Höhere mögliche Turnierblöcke Blöcke werden gesucht und an sollen an turner_erstellen.tmp.php übergeben werden
$block_higher = []; //  Array der möglichen höheren Turnierblöcke
$block_higher_str = ''; //  String der möglichen höheren Turnierblöcke

// Position des eigenen Blockes im Array der Blöcke
$chosen = array_search($ausrichter_block, Config::BLOCK);
while ($chosen >= 0) {
    $block_higher[] = Config::BLOCK[$chosen];
    $block_higher_str .= Config::BLOCK[$chosen] . ', ';
    --$chosen;
}
$block_higher_str = substr($block_higher_str, 0, -2);


$startzeit = (string)($_POST['startzeit'] ?? '');

// Formularauswertung
if (isset($_POST['create_turnier'])) {    
    $error = false;

    // Art festlegen
    $art = $_POST['art'];
    if (
        !in_array($art, ['I', 'II', 'III', 'spass'], true)
        && !(Helper::$ligacenter && in_array($art, ['final', 'fixed'], true))
    ) {
        $error = true;
        Html::error("Unbekannte Turnierart.");
    }

    // Turnierblock festlegen
    if ($art === 'I') {
        $tblock = $ausrichter_block;
    } elseif ($art === 'II') {
        if (is_numeric(strpos($block_higher_str, $_POST['block']))) {
            $tblock = $_POST['block'];
        } else {
            $error = true;
            Html::error("Block und Turnierart passen nicht zueinander.");
        }
    } elseif ($art === 'III') {
        $tblock = 'ABCDEF';
    } elseif ($art === 'fixed') {
        $tblock = $_POST['tblock'];
        if (!in_array($tblock, Config::BLOCK_ALL, true)) {
            $error = true;
            Html::error("Ungültiger Turnierblock.");
        }
    } elseif ($art === 'final') {
        $tblock = $_POST['block_final'];
        if (!in_array($tblock, Config::BLOCK_FINALE, true)) {
            $error = true;
            Html::error("Ungültiger Turnierblock.");
        }
    } elseif ($art === 'spass') {
        $tblock = '';
    } else {
        $error = true;
        Html::error("Es konnte kein Turnierblock bestimmt werden.");
    }

    // Anzahl der Plätze bzw ob 8er DKO- oder Gruppen-Spielplan
    $plaetze = (string)($_POST['plaetze'] ?? 0);
    if ($plaetze === '8 dko') {
        $plaetze = 8;
        $format = 'dko';
    } elseif ($plaetze === '8 gruppen') {
        $plaetze = 8;
        $format = 'gruppen';
    } else {
        $format = 'jgj';
    }

    // Validierung der Plätze
    if ($plaetze < 4 || $plaetze > 8) {
        $error = true;
        Html::error("Ungültige Anzahl an Turnierplätzen.");
    }
    // 4er Turniere nur über den LA
    if ($plaetze === 4 && Helper::$teamcenter) {
        $error = true;
        Html::error("Ungültige Anzahl an Turnierplätzen");
    }

    $datum = strtotime((string)($_POST['datum'] ?? ''));
    // Validierung von Datum und Startzeit nur für Ligaturniere und Ligateams
    if (
        !Helper::$ligacenter
        && in_array($art, ['I', 'II', 'III'], true)
    ) {
        // Validierung Datum
        if (
            $datum < strtotime(Config::SAISON_ANFANG)
            || $datum > strtotime(Config::SAISON_ENDE)
        ) {
            $error = true;
            Html::error("Das Datum liegt außerhalb der Saison.");
        }

        $feiertage = Feiertage::finden(date("Y", $datum));
        if (!in_array($datum, $feiertage) && date('N', $datum) < 6) {
            $error = true;
            Html::error("Das Datum liegt nicht am Wochende und ist kein bundesweiter Feiertag.");
        }
    
        if (LigaBot::time_offen_melde(date("Y-m-d", $datum)) < time()) {
            $error = true;
            Html::error("Turniere können nur vier Wochen vor dem Spieltag eingetragen werden");
        }

        // Validierung Startzeit:
        if ((date("H", strtotime($startzeit)) < 9 || date("H", strtotime($startzeit)) > 15)) {
            $error = true;
            Html::error("Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen spätestens"
                . " um 20:00&nbsp;Uhr beendet sein. Wende dich an den Ligaausschuss für spezielle"
                . " Spielzeiten.");
        }
    } elseif (
        Helper::$ligacenter
        && in_array($tblock, Config::BLOCK_FINALE, true)
    ) {
        if (
            $datum != strtotime(Config::FINALE_EINS)
            && $datum != strtotime(Config::FINALE_ZWEI)
            && $datum != strtotime(Config::FINALE_DREI)
            && $datum != strtotime(Config::FINALE_VIER)
        ) {
            $error = true;
            Html::error("Das Datum ist nicht für die Abschlussturniere vorgesehen.");
        }
    }

    $datum = date("Y-m-d", $datum);
    $tname = (string)$_POST['tname'];
    $hallenname = (string)$_POST['hallenname'];
    $strasse = (string)$_POST['strasse'];
    $plz = (int)$_POST['plz'];
    $ort = (string)$_POST['ort'];
    $haltestellen = (string)$_POST['haltestellen'];
    $hinweis = (string)$_POST['hinweis'];
    $startgebuehr = (string)$_POST['startgebuehr'];
    $organisator = (string)$_POST['organisator'];
    $handy = (string)$_POST['handy'];

    // Besprechung
    if (($_POST['besprechung'] ?? '') === 'Ja') {
        $besprechung = 'Ja';
    } else {
        $besprechung = 'Nein';
    }

    // Fixierter Turnierblock?
    $fixed = match ($art) {
        'I', 'II' => 'Nein',
        default => 'Ja'
    };

    // Eintragen des Turnieres
    if ($error) {
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht erstellt.");
    } else {
        // Turnier erstellen
        $turnier = nTurnier::get($ausrichter_team_id);
        $turnier->set_turniere_liga('art', $art)
            ->set_turniere_liga('tblock_fixed', $fixed)
            ->set_turniere_liga('tname', $tname)
            ->set_turniere_liga('tblock', $tblock)
            ->set_turniere_liga('datum', $datum)
            ->set_turniere_liga('saison', Config::SAISON)
            ->set_turniere_details('startzeit', $startzeit)
            ->set_turniere_details('besprechung', $besprechung)
            ->set_turniere_details('hinweis', $hinweis)
            ->set_turniere_details('plaetze', $plaetze)
            ->set_turniere_details('format', $format)
            ->set_turniere_details('plz', $plz)
            ->set_turniere_details('ort', $ort)
            ->set_turniere_details('strasse', $strasse)
            ->set_turniere_details('hallenname', $hallenname)
            ->set_turniere_details('haltestellen', $haltestellen)
            ->set_turniere_details('format', $format)
            ->set_turniere_details('handy', $handy)
            ->set_turniere_details('organisator', $organisator)
            ->set_turniere_details('startgebuehr', $startgebuehr);

        // Mailbot
        if (Helper::$teamcenter) MailBot::mail_neues_turnier($turnier); // Nur wenn Teams Turniere erstellen.

        // Turnier wurde erfolgreich erstellt - Weiterleitung zu Turnierdetails
        Html::info("Euer Turnier wurde erfolgreich eingetragen.");
        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->turnier_id);
    }
}