<?php

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
    if ($plaetze == '8 dko') { //TODO besser machen - Unterscheidung zwsichen dko und gruppen nicht zwangsläufig notwendig
        $plaetze = 8;
        $format = 'dko';
    } elseif ($plaetze == '8 gruppen') {
        $plaetze = 8;
        $format = 'gruppen';
    } else {
        $format = 'jgj';
    }

    // Leere Felder können eigentlich nicht auftreten (nur durch html-Manipulation), aber sicherheitshalber das hier...
    if (
        empty($plaetze) || empty($startzeit) || empty($hallenname) || empty($strasse) || empty($plz) || empty($ort)
        || empty($hinweis) || empty($organisator) || empty($handy)
    ) {
        $error = true;
        Html::error("Bitte alle nicht optionalen Felder ausfüllen.");
    }

    // Validierung Startzeit:
    if ($startzeit != $turnier->get_startzeit() && Helper::$teamcenter) {
        if ($turnier->get_art() === 'final') {
            $error = true;
            Html::error("Die Startzeit bei Finalturnieren kann nur vom Ligaausschuss geändert werden.");
        }
        if (
            $startzeit != $turnier->get_startzeit()
            && (date("H", strtotime($startzeit)) < 9 || date("H", strtotime($startzeit)) > 14)
            && Helper::$teamcenter
        ) {
            $error = true;
            Html::error("Turniere dürfen frühestens um 9:00&nbsp;Uhr beginnen und müssen spätestens um 20:00&nbsp;Uhr beendet sein");
        }
    }

    // Validierung der Plätze
    if ($plaetze != $turnier->get_plaetze() && Helper::$teamcenter) {
        if ($turnier->get_art() === 'final') { //Anzahl der Plätze darf nur geändert werden, wenn es sich nicht um ein Finalturnier handelt
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
        $turnier->get_phase() === 'spielplan'
        && $turnier->get_plaetze() != $plaetze
        && !Helper::$ligacenter
    ) {
        $error = true;
        Html::error("Die Anzahl der Plätze kann in der Spielplanphase nicht mehr geändert werden. Bitte wende dich unter "
            . Env::LAMAIL . " an den Ligaaussschuss.");
    }

    // Keine Änderungen in der Ergebnisphase
    if (
        $turnier->get_phase() === 'ergebnis'
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

    $tblock = $turnier->get_tblock();
    $fixed = $turnier->get_tblock_fixed();
    $art = $turnier->get_art();
    
    $erweitern = false; // Wird auf true gesetzt, wenn der Turnierblock erweitert werden soll

    // Um den nächst höheren Buchstaben erweitern
    if (isset($_POST['block_erweitern'])) {
        if ($turnier->is_erweiterbar_blockhoch()) {
            $chosen = array_search($turnier->get_tblock(), Config::BLOCK_ALL);
            if (($_POST['block_erweitern'] ?? '') === Config::BLOCK_ALL[$chosen - 1]) {
                $tblock = Config::BLOCK_ALL[$chosen - 1];
                $fixed = $turnier->get_tblock_fixed();
                $erweitern = true;
            }
        } else {
            $error = true;
            Html::error("Das Turnier kann nicht um den nächsthöheren Block erweitert werden.");
        }
    }

    // Auf ABCDEF erweitern
    if (isset($_POST['block_frei'])) {
        if ($turnier->is_erweiterbar_blockfrei()) {
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
    if (!$error) {
        // Turnierblock erweitern und mögliche Teams der Warteliste aufnehmen
        if ($erweitern) {
            $turnier->set_tblock($tblock)
                    ->set_fixed_tblock($fixed)
                    ->set_art($art)
                    ->set_database()
                    ->spieleliste_auffuellen();
            Html::info("Turnier wurde erweitert");
        }

        // Mail an den Ligaausschuss, wenn Plätze, Startzeit, Ort oder Format im Teamcenter geändert wurden
        if (
            Helper::$teamcenter
            && (
                $turnier->get_startzeit() !== $startzeit
                || $turnier->get_plaetze() !== $plaetze
                || $turnier->get_ort() !== $ort
                || $turnier->get_format() !== $format
            )
        ) {
            MailBot::mail_turnierdaten_geaendert($turnier);
        }

        // Ändern der Turnierdetails
        $turnier->set_startzeit($startzeit)
                ->set_besprechung($besprechung)
                ->set_plaetze($plaetze)
                ->set_format($format)
                ->set_hallennamen($hallenname)
                ->set_strasse($strasse)
                ->set_plz($plz)
                ->set_ort($ort)
                ->set_haltestelle($haltestellen)
                ->set_startgebuehr($startgebuehr)
                ->set_organisator($organisator)
                ->set_handy($handy)
                ->set_hinweis($hinweis)
                ->set_database();

        // Spielen-Liste aktualisieren, wenn die Anzahl der Plätze geändert wurde        
        if (
            $turnier->is_erweiterbar_plaetze()
            && $turnier->get_plaetze() < $plaetze
        ) {
            $turnier->spieleliste_auffuellen();
        }

        Html::info("Turnierdaten wurden geändert");
        Helper::reload('/liga/turnier_details.php?turnier_id=' . $turnier->get_turnier_id());
    } else {
        Html::error("Es ist ein Fehler aufgetreten. Turnier wurde nicht geändert - alle Änderungen bitte neu eingeben.");
    }
}