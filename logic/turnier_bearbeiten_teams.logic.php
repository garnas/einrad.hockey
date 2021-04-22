<?php
// Kann das Turnier erweitert werden?
if (
    $turnier->details['phase'] === 'melde'
    && strlen($turnier->details['tblock']) < 3
    && $turnier->details['tblock'] !== 'AB'
    && $turnier->details['tblock'] !== 'A'
    && ($turnier->details['art'] === 'I' || $turnier->details['art'] === 'II')
) {
    $blockhoch = true;
} else {
    $blockhoch = false;
}

// Kann das Turnier auf ABCDEF erweitert werden?
if (
    $turnier->details['phase'] === 'melde'
    && $turnier->details['art'] !== 'III'
    && ($turnier->details['art'] === 'I' || $turnier->details['art'] === 'II')
) {
    $blockfrei = true;
} else {
    $blockfrei = false;
}

// Formularauswertung
if (isset($_POST['change_turnier'])) {

    $error = false;
    $hallenname = $_POST['hallenname'];
    $strasse = $_POST['strasse'];
    $plz = $_POST['plz'];
    $ort = $_POST['ort'];
    $haltestellen = $_POST['haltestellen'];
    $hinweis = $_POST['hinweis'];
    $startgebuehr = $_POST['startgebuehr'];
    $organisator = $_POST['organisator'];
    $handy = $_POST['handy'];
    $startzeit = $_POST['startzeit'];
    $plaetze = $_POST['plaetze'];

    // Besprechung
    if (($_POST['besprechung'] ?? '') === 'Ja') {
        $besprechung = 'Ja';
    } else {
        $besprechung = 'Nein';
    }

    // Anzahl der Plätze bzw ob 8er DKO- oder Gruppen-Spielplan
    if ($plaetze == '8 dko') {
        $plaetze = 8;
        $format = 'dko';
    } elseif ($plaetze == '8 gruppen') {
        $plaetze = 8;
        $format = 'gruppen';
    } else {
        $format = 'jgj';
    }

    // Leere Felder können eigentlich nicht auftreten (nur durch html-Manipulation), aber sicherheitshalber dass hier...
    if (
        empty($plaetze) || empty($startzeit) || empty($hallenname) || empty($strasse) || empty($plz) || empty($ort)
        || empty($hinweis) || empty($organisator) || empty($handy)
    ) {
        $error = true;
        Html::error("Bitte alle nicht optionalen Felder ausfüllen.");
    }

    // Validierung Startzeit:
    if ($startzeit != $turnier->details['startzeit'] && Helper::$teamcenter) {
        if ($turnier->details['art'] === 'final') {
            $error = true;
            Html::error("Die Startzeit bei Abschlussturnieren kann nur vom Ligaausschuss geändert werden.");
        }
        if (
            $startzeit != $turnier->details['startzeit']
            && (date("H", strtotime($startzeit)) < 9 || date("H", strtotime($startzeit)) > 14)
            && Helper::$teamcenter
        ) {
            $error = true;
            Html::error("Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen spätestens um 20:00&nbsp;Uhr beendet sein");
        }
    }

    // Validierung der Plätze
    if ($plaetze != $turnier->details['plaetze'] && Helper::$teamcenter) {
        if ($turnier->details['art'] === 'final') { //Anzahl der Plätze darf nur geändert werden, wenn es sich nicht um ein Finalturnier handelt
            Html::error("Das Ändern der Anzahl der Plätze ist bei Abschlussturnieren können nur vom Ligaausschuss geändert werden.");
            $error = true;
        }
        if ($plaetze < 5 || $plaetze > 8) {
            $error = true; // 4er nur via Ligaausschuss
            Html::error("Ungültige Anzahl an Turnierplätzen.");
        }
    }

    // Keine Änderung der Plätze in der Spielplanphase
    if (
        $turnier->details['phase'] === 'spielplan'
        && $turnier->details['plaetze'] != $plaetze
        && !Helper::$ligacenter
    ) {
        $error = true;
        Html::error("Die Anzahl der Plätze kann in der Spielplanphase nicht mehr geändert werden. Bitte wende dich unter "
            . Env::LAMAIL . " an den Ligaaussschuss.");
    }

    // Keine Änderungen  in der Ergebnisphase
    if (
        $turnier->details['phase'] === 'ergebnis'
        && !Helper::$ligacenter
    ) {
        $error = true;
        Html::error("Turniere können in der Ergebnisphase nicht mehr geändert werden. Bitte wende dich unter "
            . Env::LAMAIL . " an den Ligaaussschuss.");
    }

    // Block erweitern

    // Es wurden beide Häkchen gesetzt
    if (isset($_POST['block_frei'], $_POST['block_erweitern'])) {
        $error = true;
        Html::error("Bitte entweder um den nächsthöheren Block oder auf ABCDEF öffnen.");
    }

    $tblock = $turnier->details['tblock'];
    $fixed = $turnier->details['tblock_fixed'];
    $art = $turnier->details['art'];
    $erweitern = false; // Wird auf true gesetzt, wenn der Turnierblock erweitert werden soll

    // Um den nächst höheren Buchstaben erweitern
    if (isset($_POST['block_erweitern'])) {
        if ($blockhoch) {
            $chosen = array_search($turnier->details['tblock'], Config::BLOCK_ALL);
            if (($_POST['block_erweitern'] ?? '') === Config::BLOCK_ALL[$chosen - 1]) {
                $tblock = Config::BLOCK_ALL[$chosen - 1];
                $fixed = $turnier->details['tblock_fixed'];
                $erweitern = true;
            }
        } else {
            $error = true;
            Html::error("Das Turnier kann nicht um den nächsthöheren Block erweitert werden.");
        }
    }

    // Auf ABCDEF erweitern
    if (isset($_POST['block_frei'])) {
        if ($blockfrei) {
            if (($_POST['block_frei'] ?? '') === 'ABCDEF') {
                $tblock = 'ABCDEF';
                $fixed = 'Ja';
                $art = 'III';
                $erweitern = true;
            }
        } else {
            $error = true;
            Html::error("Das Turnier kann nicht auf ABCDEF erweitert werden.");
        }
    }

    // Ändern der Turnierdaten
    if ($error) {
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    } else {
        // Turnierblock erweitern
        if ($erweitern) {
            $turnier->set_liga('tblock', $tblock)
                    ->set_liga('tblock_fixed', $fixed)
                    ->set_liga('art', $art)
                    ->spieleliste_auffuellen(); // Spielen-Liste auffuellen
            Html::info("Turnier wurde erweitert");
        }

        // Mail an den Ligaausschuss, wenn Platze, Startzeit oder Ort geändert worden sind
        if (
            Helper::$teamcenter
            && (
                $turnier->details['startzeit'] !== $startzeit
                || $turnier->details['plaetze'] !== $plaetze
                || $turnier->details['ort'] !== $ort
                || $turnier->details['format'] !== $format
            )
        ) {
            MailBot::mail_turnierdaten_geaendert($turnier);
        }

        // Ändern der Turnierdetails
        $turnier->set('startzeit', $startzeit)
                ->set('besprechung', $besprechung)
                ->set('plaetze', $plaetze)
                ->set('format', $format)
                ->set('hallenname', $hallenname)
                ->set('strasse', $strasse)
                ->set('plz', $plz)
                ->set('ort', $ort)
                ->set('haltestellen', $haltestellen)
                ->set('startgebuehr', $startgebuehr)
                ->set('organisator', $organisator)
                ->set('handy', $handy)
                ->set('hinweis', $hinweis);

        Html::info("Turnierdaten wurden geändert");
        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->id);
    }
}