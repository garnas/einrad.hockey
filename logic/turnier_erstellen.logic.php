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

    switch ($art) {
        case 'I':
            $tblock = $ausrichter_block;
            break;

        case 'II':
            if (in_array($_POST['block'], $block_higher)) {
                $tblock = $_POST['block'];
            } else {
                $error = true;
                Html::error("Block und Turnierart passen nicht zueinander.");
            }
            break;

        case 'III':
            $tblock = 'ABCDEF';
            break;

        case 'fixed':
            if (in_array($_POST['block'], Config::BLOCK_ALL, true)) {
                $tblock = $_POST['block'];
            } else {
                $error = true;
                Html::error("Ungültiger Turnierblock.");
            }
            break;

        case 'final':
            if (in_array($_POST['block'], Config::BLOCK_FINALE, true)) {
                $tblock = $_POST['block'];
            } else {
                $error = true;
                Html::error("Ungültiger Turnierblock.");
            }
            break;

        case 'spass':
            $tblock = '';
            break;

        default:
            $error = true;
            Html::error("Es konnte kein Turnierblock bestimmt werden.");
            break;
    }

    // Anzahl der Plätze bzw ob 8er DKO- oder Gruppen-Spielplan
    $plaetze = $_POST['plaetze'];
    switch ($plaetze) {
        case '8 dko':
            $plaetze = 8;
            $format = 'dko';
            break;
        
        case '8 gruppen':
            $plaetze = 8;
            $format = 'gruppen';
            break;

        default:
            $format = 'jgj';
            break;
    }
    $plaetze = (int)$plaetze;

    // Validierung der Plätze
    if (Helper::$teamcenter && ($plaetze < 4 || $plaetze > 8)) {
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
        in_array($art, Config::TURNIER_ARTEN, true)
    ) {
        // Validierung Datum
        if (
                Helper::$teamcenter &&
                (
                    $datum < strtotime(Config::SAISON_ANFANG)
                    || ($datum > strtotime(Config::SAISON_ENDE)
                )
            )
        ) {
            $error = true;
            Html::error("Das Datum liegt außerhalb der Saison.");
        }

        $feiertage = Feiertage::finden(date("Y", $datum));
        if (Helper::$teamcenter && !in_array($datum, $feiertage) && date('N', $datum) < 6) {
            $error = true;
            Html::error("Das Datum liegt nicht am Wochende und ist kein bundesweiter Feiertag.");
        }
    
        if (LigaBot::time_offen_melde(date("Y-m-d", $datum)) < time() && Helper::$teamcenter) {
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
//    } elseif (
//        Helper::$ligacenter
//        && in_array($tblock, Config::BLOCK_FINALE, true)
//    ) {
//        if (
//            $datum != strtotime(Config::FINALE_EINS)
//            && $datum != strtotime(Config::FINALE_ZWEI)
//            && $datum != strtotime(Config::FINALE_DREI)
//            && $datum != strtotime(Config::FINALE_VIER)
//        ) {
//            $error = true;
//            Html::error("Das Datum ist nicht für die Abschlussturniere vorgesehen.");
//        }
    }

    $datum = date("Y-m-d", $datum);
    $tname = (string)$_POST['tname'];
    $hallenname = (string)$_POST['hallenname'];
    $strasse = (string)$_POST['strasse'];
    $plz = (string)$_POST['plz'];
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
    if (!$error) {
        // Turnier erstellen
        $turnier = nTurnier::set_turnier($ausrichter_team_id);

        $turnier->set_datum($datum)
            ->set_art($art)
            ->set_fixed_tblock($fixed)
            ->set_tname($tname)
            ->set_tblock($tblock)
            ->set_saison(Config::SAISON)
            ->set_startzeit($startzeit)
            ->set_besprechung($besprechung)
            ->set_hinweis($hinweis)
            ->set_plaetze($plaetze)
            ->set_format($format)
            ->set_plz($plz)
            ->set_ort($ort)
            ->set_strasse($strasse)
            ->set_hallennamen($hallenname)
            ->set_haltestelle($haltestellen)
            ->set_handy($handy)
            ->set_organisator($organisator)
            ->set_startgebuehr($startgebuehr)
            ->set_database();

        // Mailbot, wenn Teams neue Turniere erstellen
        if (Helper::$teamcenter) {
            MailBot::mail_neues_turnier($turnier);
        }

        // Turnier wurde erfolgreich erstellt - Weiterleitung zu Turnierdetails
        Html::info("Euer Turnier wurde erfolgreich eingetragen.");
        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->get_turnier_id());
    } else {
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht erstellt.");
    }
}