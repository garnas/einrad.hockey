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

// Formularauswertung
if (isset($_POST['create_turnier'])) {
    $error = false;
    // Besprechung
    if (($_POST['besprechung'] ?? '') == 'Ja') {
        $besprechung = 'Ja';
    } else {
        $besprechung = 'Nein';
    }
    // Turnierblock wird zugewiesen:
    $art = $_POST['art'];
    if ($art == "I" && isset($_POST['block'])) {
        $tblock = $ausrichter_block;
        $phase = "offen";
        $fixed = "Nein";
    } elseif ($art == "II" && isset($_POST['block'])) {
        // Validierung gewählter Turnierblock
        // Ist der Block im String der höheren Turnierblöcke vorhanden?
        if (is_numeric(strpos($block_higher_str, $_POST['block']))) {
            $tblock = $_POST['block'];
        } else {
            $error = true;
            Html::error("Block und Turnierart passen nicht zueinander");
        }
        $phase = "offen";
        $fixed = "Nein";
    } elseif ($art == "III" && isset($_POST['block'])) {
        $tblock = "ABCDEF";
        $phase = "offen";
        $fixed = "Ja";
    } elseif ($art == "spass" && isset($_POST['block'])) {
        $tblock = "spass";
        $phase = "";
        $fixed = "Ja";
    } elseif (Helper::$ligacenter && $art == "fixed" && isset($_POST['block_fixed'])) {
        $tblock = $_POST['block_fixed'];
        $phase = "offen";
        $fixed = "Ja";
    } elseif (Helper::$ligacenter && $art == "final") {
        $tblock = "final";
        $phase = "";
        $fixed = "Ja";
    } else {
        $error = true;
        Html::error("Es konnte keine Turnierart festgelegt werden");
    }

    // Validierung des Turnierblocks
    if (!($tblock == "final" || $tblock == "ABCDEF" || $tblock == "spass" || in_array($tblock, Config::BLOCK))) {
        $error = true;
        Html::error("Das Turnier hat einen ungültigen Turnierblock");
    }

    // Anzahl der Plätze bzw ob 8er DKO- oder Gruppen-Spielplan
    $plaetze = $_POST['plaetze'];
    if ($plaetze == '8 dko') {
        $plaetze = 8;
        $spielplan = 'dko';
    } elseif ($plaetze == '8 gruppen') {
        $plaetze = 8;
        $spielplan = 'gruppen';
    } else {
        $spielplan = 'jgj';
    }

    // Validierung der Plätze
    if ($plaetze < 4 || $plaetze > 8) {
        $error = true;
        Html::error("Ungültige Anzahl an Turnierplätzen");
    }
    // 4er Turniere nur über den LA
    if ($plaetze == 4 && Helper::$teamcenter) {
        $error = true;
        Html::error("Ungültige Anzahl an Turnierplätzen");
    }

    $datum = date("Y-m-d", strtotime($_POST['datum'])); // Hinzugefügt, falls ein anderes Datumsformat von dem HTML-Form übermittelt wird
    $startzeit = $_POST['startzeit'];

    // Validierung des ausgewählten Turnierdatums, falls man nicht als la_eingeloggt ist.
    if (!(Helper::$ligacenter || $art == "spass")) {
        $datum_unix = strtotime($datum);
        if ($datum_unix < strtotime(Config::SAISON_ANFANG) || $datum_unix > strtotime(Config::SAISON_ENDE)) {
            $error = true;
            Html::error("Das Datum liegt außerhalb der Saison");
        }
        $feiertage = Feiertage::finden(date("Y", $datum_unix));
        if (!in_array($datum_unix, $feiertage) && date('N', $datum_unix) < 6) {
            $error = true;
            Html::error("Das Datum liegt nicht am Wochende und ist kein bundesweiter Feiertag");
        }
        if (LigaBot::time_offen_melde($datum) < time()) {
            // if ($datum_unix > (strtotime(Config::SAISON_ANFANG) + 4*7*24*60*60)){ // Ausnahme für die ersten vier Wochen
            $error = true;
            Html::error("Turniere können nur vier Wochen vor dem Spieltag eingetragen werden");
            // }
        }
        // Validierung Startzeit:
        if ((date("H", strtotime($startzeit)) < 9 || date("H", strtotime($startzeit)) > 14) and !Helper::$ligacenter) {
            $error = true;
            Html::error("Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen spätestens um 20:00&nbsp;Uhr beendet sein");
        }
    }

    $tname = $_POST['tname'];
    $hallenname = $_POST['hallenname'];
    $strasse = $_POST['strasse'];
    $plz = $_POST['plz'];
    $ort = $_POST['ort'];
    $haltestellen = $_POST['haltestellen'];
    $hinweis = $_POST['hinweis'];
    $startgebuehr = $_POST['startgebuehr'];
    $organisator = $_POST['organisator'];
    $handy = $_POST['handy'];

    // Eintragen des Turnieres
    if ($error) {
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht erstellt.");
    } else {
        // Turnier erstellen
        $turnier = Turnier::create_turnier($tname, $ausrichter_team_id, $startzeit, $besprechung, $art, $tblock, $fixed, $datum,
            $plaetze, $spielplan, $hallenname, $strasse, $plz, $ort, $haltestellen, $hinweis, $startgebuehr,
            $organisator, $handy, $phase); // Vom Typ Turnier

        // Mailbot
        if (Helper::$teamcenter) MailBot::mail_neues_turnier($turnier); // Nur wenn Teams turnier erstellen.
        Html::info("Euer Turnier wurde erfolgreich eingetragen!");
        header('Location: ../liga/turnier_details.php?turnier_id=' . $turnier->id);
        die();
    }
}