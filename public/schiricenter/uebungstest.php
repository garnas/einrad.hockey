<?php # -*- php -*-
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php'; # Autoloader und Session, muss immer geladen werden!
# require_once '../../logic/session_team.logic.php'; # Nur im Teamcenter zugreifbar

# Antwort auswerten oder neue Frage stellen?
if (isset($_POST['beantworten'])) {
    $fragen = $_SESSION['sc_test_fragen'];
} else {
    // Für Debug-Modus
    if (isset($_POST['ausgewaehlte_nummer'])
        ) {
        $naechste_frage = $_POST['ausgewaehlte_nummer'];
        if (!ctype_digit($naechste_frage)) { # Input enthält nicht nur Ziffern
            if (
                isset($_SESSION['frage_id'])
                && empty($naechste_frage)
            ) {
                $naechste_frage = $_SESSION['frage_id'] + 1; # (n+1) auswählen
            } else {
                $naechste_frage = '42';
            }
        }
        $fragen = SchiriTest::get_fragen('L', '*', 1, $naechste_frage);
    } else {
        $fragen = SchiriTest::get_fragen('L', '*', 1); # zufällige Frage
    }
    $_SESSION['sc_test_fragen'] = $fragen;
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = 'Übungs-Schiritest der Deutschen Einradhockeyliga';
include '../../templates/header.tmp.php'; # Html-header und Navigation
if (isset($DEBUGMODUS)) { # Start Debug Modus
    echo '<form method="post"> ' .
        '<div class="w3-card w3-panel w3-leftbar w3-border-red w3-pale-red"> ' .
        'Frage Nr.: <input type="text" size="3" name="ausgewaehlte_nummer"> ' .
        '<input type="submit" value="auswählen"> ' .
        '(leere Eingabe erhöht Fragennummer um 1) (Debug Modus)</div></form>';
} # Ende Debug Modus
echo '<form method="post">';
foreach ($fragen as $frage_id => $frage) { # Schleife über alle Fragen:
    echo '<div class="w3-section w3-display-container">';
    SchiriTest::frage_anzeigen($frage_id, $frage);
    if (isset($_POST['beantworten'])) { # Test auswerten:
        SchiriTest::auswertung_anzeigen($frage_id, $frage);
    } else { # Test anzeigen:
        SchiriTest::antworten_anzeigen($frage_id, $frage);
    }
    echo '</div>';
}
$_SESSION['frage_id'] = $frage_id; # Fragennummer abspeichern
if (isset($_POST['beantworten'])) {
    echo '<p><button type="submit" class="w3-button w3-block w3-primary" ' .
        'name="neue_fragen"> <i class="material-icons">cached</i> ' .
        'Nächste Frage</button></p>';
} else {
    echo '<p><button type="submit" class="w3-button w3-block w3-primary" ' .
        'name="beantworten"> <i class="material-icons">check_circle_outline</i> ' .
        'Frage beantworten</button></p>';
}
echo '</form>';

# Start Debug Modus
if (isset($DEBUGMODUS)) { # Start Debug Modus
    $debuginfo = "Kategorie:         " . $frage['kategorie'];
    $debuginfo .= "<BR>LJBF:          " . $frage['LJBF'];
    $debuginfo .= "<BR>richtig:       ";
    foreach ($frage['richtig'] as $i) {
        $debuginfo .= $i . " ";
    }
    $debuginfo .= "<BR>Regelnummer:   " . $frage['regelnr'];
    $debuginfo .= "<BR>Punkte:        " . $frage['punkte'];
    $debuginfo .= "<BR>bestätigt:     " . $frage['bestaetigt'];
    $debuginfo .= "<BR>interne Notiz: " . $frage['interne_notiz'];
    Html::message('error', $debuginfo, "Infos zu Frage Nr. " . $frage_id .
        " (Debug Modus):", esc: false);
} # Ende Debug Modus

include '../../templates/footer.tmp.php';

?>
