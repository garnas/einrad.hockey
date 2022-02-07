<?php # -*- php -*-
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php'; # Autoloader und Session, muss immer geladen werden!
# require_once '../../logic/session_team.logic.php'; # Nur im Teamcenter zugreifbar

if (isset($_GET['md5sum'])) {
    [$pruefling_id, $pruefling, $test_level, $fragen, $neu] = SchiriTest::offizieller_test($_GET['md5sum']);
}

if (isset($test_level)) {
    # Fragen aus den einzelnen Kategorien auswählen:
    $lev_info = SchiriTest::lev_infos[$test_level];
    $levelname   = $lev_info['name'];
    $anzahl      = $lev_info['anzahl'];
    $timelimit   = $lev_info['timelimit'];
    $richtig_min = $lev_info['richtig_min'];
} else {
    die('Ungültige URL');
}

# Antwort auswerten oder neue Frage stellen?
if (isset($_POST['beantworten'])) {
    $alle_antworten_user = array();
    $pruefling = $_SESSION['pruefling'];
    $titel = 'Auswertung des Schiritests';
    $fragen = $_SESSION['sc_test_fragen'];
    $richtig = 0; # Zähler für richtige Antworten
    foreach ($fragen as $frage_id => $frage) {
        $antworten_user = $_POST['abgabe'][$frage_id] ?? [];
        $alle_antworten_user[] = $antworten_user;
        if (SchiriTest::validate_frage($frage_id, $antworten_user)) {
            $richtig++;
        }
    }
    if (!empty($pruefling)){
        SchiriTest::testergebnis_melden($fragen, $richtig, $alle_antworten_user);
    }
} else {
    if (isset($_GET['md5sum'])) {
        if (!$neu) {
            exit('<H1>Ungültiger Test (wurde schon gestartet)</H1>');
        }
        $zeitstempel = date('Y-m-d H:i:s'); # heutiges Datum + Uhrzeit
        $sql = "UPDATE schiri_ergebnis SET t_gestartet = ? WHERE md5sum = ?;";
        $params = [$zeitstempel, $_GET['md5sum']];
        db::$db->query($sql, $params)->log();
        $titel = 'Schiritest (' . $levelname . ') für ' . $pruefling;
    } else {
        $pruefling = '';
        $titel = 'Übungstest (' . $levelname . ')';
        $frage_id = 0; # zufällige Frage
        if (($test_level=='L') && (isset($_POST['ausgewaehlte_nummer']))) {
            $frage_id = $_POST['ausgewaehlte_nummer'];
            if (!ctype_digit($frage_id)) { # input ist kein integer
                $frage_id = 0; # zufällige Frage
            }
        }
        $fragen = 
            SchiriTest::get_fragen($test_level,  0, $anzahl[0],  $frage_id) + 
            SchiriTest::get_fragen($test_level,  1, $anzahl[1],  $frage_id) + 
            SchiriTest::get_fragen($test_level,  2, $anzahl[2],  $frage_id) + 
            SchiriTest::get_fragen($test_level,  3, $anzahl[3],  $frage_id) + 
            SchiriTest::get_fragen($test_level,  4, $anzahl[4],  $frage_id) + 
            SchiriTest::get_fragen($test_level,  5, $anzahl[5],  $frage_id) + 
            SchiriTest::get_fragen($test_level,  6, $anzahl[6],  $frage_id) + 
            SchiriTest::get_fragen($test_level,  7, $anzahl[7],  $frage_id) + 
            SchiriTest::get_fragen($test_level,  8, $anzahl[8],  $frage_id) + 
            SchiriTest::get_fragen($test_level,  9, $anzahl[9],  $frage_id) + 
            SchiriTest::get_fragen($test_level, 10, $anzahl[10], $frage_id) +
            SchiriTest::get_fragen($test_level, 11, $anzahl[11], $frage_id); 
    }
    $_SESSION['sc_test_fragen'] = $fragen;
    $_SESSION['pruefling'] = $pruefling;
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = $titel;
include '../../templates/header.tmp.php'; # Html-header und Navigation

if ($test_level=='L') {
    echo '<p><form method="post" class="w3-panel w3-center w3-indigo"><P> ' .
        '(Fragen-ID: <input type="text" size="3" name="ausgewaehlte_nummer">) ' .
        '<input type="submit" value="Neue Frage"> ' . '</P></form>';
}

# Start Debug Modus
if (Env::DEBUGMODUS) {
    $index = 0;
    echo '<table class="w3-table w3-pale-red w3-bordered">';
    echo '<tr><td>Nr.</td><td>id</td><td>Kat.</td><td>Level</td><td>Frage</td></tr>';
    foreach ($fragen as $frage) {
        echo '<tr><td>' . ++$index . '</td>';
        echo '<td>' . $frage['frage_id'] . '</td>';
        echo '<td>' . $frage['kategorie'] . '</td>';
        echo '<td>' . $frage['LJBF'] . '</td>';
        echo '<td>' . $frage['frage'] . '</td></tr>';
    }
    echo '</table>';
} # Ende Debug Modus

if (isset($_POST['beantworten'])) { # Test auswerten:
    if ($pruefling == '') {
        $text_bestanden = ' Herzlichen Glückwunsch, du hast den Übungstest bestanden! ';
        $text_durchgefallen = ' Du hast den Übungstest leider nicht bestanden. ';
    } else {
        echo '<P>Danke für das Ausfüllen des Schiritests, deine Antworten sind an den ';
        echo 'Ligaausschuss geschickt worden.';
        $text_bestanden = ' Herzlichen Glückwunsch, ' . $pruefling . ', du hast bestanden! ';
        $text_durchgefallen = ' Du hast leider nicht bestanden, ' . $pruefling . '. ';
    }
    if ($richtig >= $richtig_min) { # bestanden:
        echo '<H1 class="w3-center w3-text-green">' .
            Html::icon("sentiment_satisfied_alt", class:"md-36") . $text_bestanden .
            Html::icon("sentiment_satisfied_alt", class:"md-36") . '</H1>';
    } else {
        echo '<H1 class="w3-center w3-text-red">' .
            Html::icon("sentiment_very_dissatisfied", class: "md-36") . $text_durchgefallen .
            Html::icon("sentiment_very_dissatisfied", class: "md-36") . '</H1>';
    }
    echo '<H4>Es wurden ' . $richtig . ' von ' . count($fragen) . ' Fragen ';
    echo 'richtig beantwortet, eine ausführliche Auswertung findest du hier:</H4>';
    echo '<UL><LI>Deine Antworten werden mit einem Häkchen im Kreis angezeigt.</LI>';
    echo '<LI>Der grüne bzw. rote Daumen zeigt, ob deine Antwort stimmt.</LI>';
    echo '<LI>Die richtigen Antworten sind jetzt fett gedruckt, die falschen ';
    echo 'sind grau und durchgestrichen.</LI>';
    echo '<LI>Die entsprechende Regel wird in einem grünen Kasten angezeigt. Bei ';
    echo 'manchen Fragen gibt es auch noch eine zusätzliche Erklärung.</LI></UL>';
} else { # Test anzeigen:
    echo '<H2>' . $titel . '</H2><UL>';
    if ($test_level!='L') {
        echo '<LI>Der Test besteht aus ' . count($fragen) . ' Fragen.</LI>';
        echo '<LI>Du hast ' . $timelimit . ' Minuten Zeit.</LI>';
    }
    echo '<LI>Es können mehrere Antwortmöglichkeiten richtig sein.</LI>';
    echo '<LI>Mindestens 1 Antwort ist immer richtig.</LI>';
    echo '<LI>In den Beispielen spielt Team "Rot" gegen Team "Blau".</LI>';
    if ($pruefling != '') {
        echo '<LI>Achtung: Seite nicht neu laden, während der Test ausgefüllt wird!</LI>';
    }
    echo '</UL>';
    if ($test_level!='L') { # Timer, außer für Lehrgang
        echo '<div class="w3-center w3-white w3-bottombar w3-border-primary"';
        echo 'style="position: sticky; top: 0; z-index: 1000;">';
        Html::countdown(time() + 60*$timelimit);
        echo '</div>';
    }
}

echo '<form method="post">';
$frage_index = 0;
foreach ($fragen as $frage_id => $frage) { # Schleife über alle Fragen:
    echo '<div class="w3-section w3-display-container">';
    $frage_index++;
    if ($test_level=='L') {
        SchiriTest::frage_anzeigen($frage_id, 0, $frage); # Fragen-Nr. nicht anzeigen
    } else {
        SchiriTest::frage_anzeigen($frage_id, $frage_index, $frage);
    }
    if (isset($_POST['beantworten'])) { # Test auswerten:
        SchiriTest::auswertung_anzeigen($frage_id, $frage);
    } else { # Test anzeigen:
        SchiriTest::antworten_anzeigen($frage_id, $frage);
    }
    echo '</div>';
    if (Env::DEBUGMODUS) { # Start Debug Modus
        $debuginfo = "frage_id:      " . $frage_id;
        $debuginfo .= "<BR>Kategorie: " . $frage['kategorie'];
        $debuginfo .= "<BR>LJBF:      " . $frage['LJBF'];
        $debuginfo .= "<BR>richtig:   ";
        foreach ($frage['richtig'] as $i) {
            $debuginfo .= $i . " ";
        }
        $debuginfo .= "<BR>Regelnummer:   " . $frage['regelnr'];
        $debuginfo .= "<BR>interne Notiz: " . $frage['interne_notiz'];
        echo '<p class="w3-block w3-pale-red">' . $debuginfo . '</p>';
    } # Ende Debug Modus
} # end foreach fragen
$_SESSION['frage_id'] = $frage_id; # Fragennummer abspeichern
if (!isset($_POST['beantworten'])) {
    if ($test_level!='L') {
        echo '<h3 class="w3-topbar">Fertig!</h3>';
        echo '<P>Du kannst dir alle Fragen nochmals ansehen, und du kannst deine ';
        echo 'Antworten jetzt noch ändern. Dann bitte auf "Test abgeben" klicken, ';
        echo 'danach sind keine Änderungen mehr möglich.</P>';
    }
    echo '<button type="submit" class="w3-button w3-hover-indigo w3-block w3-primary" ';
    echo 'name="beantworten"><i class="material-icons">check_circle_outline</i> ';
    echo 'Test abgeben</button>';
}
echo '</form>';
include '../../templates/footer.tmp.php';
