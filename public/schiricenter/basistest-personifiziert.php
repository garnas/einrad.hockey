<?php # -*- php -*-
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php'; # Autoloader und Session, muss immer geladen werden!
# require_once '../../logic/session_team.logic.php'; # Nur im Teamcenter zugreifbar

$levelname = array('J'=>'Junior', 'B'=>'Basis', 'F'=>'Fortgeschrittene');

# Antwort auswerten oder neue Frage stellen?
if (isset($_POST['beantworten'])) {
    $pruefling = $_SESSION['pruefling'];
    $fragen = $_SESSION['sc_test_fragen'];
    $richtig = 0; # Zähler für richtige Antworten
    foreach ($fragen as $frage_id => $frage) {
        $antworten_user = $_POST['abgabe'][$frage_id] ?? [];
        if (SchiriTest::validate_frage($frage_id, $antworten_user)) {
            $richtig++;
        }
    }
} else {
    if (isset($_GET['md5sum'])) {
        [$pruefling, $level, $fragen] = SchiriTest::personifizierter_test($_GET['md5sum']);
        $titel = 'Multiple-Choice Schiritest (' . $levelname[$level] . ') für ' . $pruefling;
    } else {
        $pruefling = '';
        $level = 'B'; # Basis
        $titel = 'Multiple-Choice Basis Schiritest (Übungstest)';
        $fragen01 = SchiriTest::get_fragen($level,  '1', 2); # Vor dem Spiel / Rund ums Spiel
        $fragen02 = SchiriTest::get_fragen($level,  '2', 3); # Schiedsrichterverhalten
        $fragen03 = SchiriTest::get_fragen($level,  '3', 1); # Handzeichen
        $fragen04 = SchiriTest::get_fragen($level,  '4', 1); # Penaltyschießen
        $fragen05 = SchiriTest::get_fragen($level,  '5', 3); # Vorfahrt
        $fragen06 = SchiriTest::get_fragen($level,  '6', 3); # Übertriebene Härte
        $fragen07 = SchiriTest::get_fragen($level,  '7', 3); # Eingriff ins Spiel
        $fragen08 = SchiriTest::get_fragen($level,  '8', 6); # Sonstige Fouls
        $fragen09 = SchiriTest::get_fragen($level,  '9', 4); # Torschüsse
        $fragen10 = SchiriTest::get_fragen($level, '10', 1); # Zeitstrafen/Unsportlichkeiten
        $fragen11 = SchiriTest::get_fragen($level, '11', 3); # Strafen
        $fragen = $fragen01 + $fragen02 + $fragen03 + $fragen04 + $fragen05 + $fragen06 +
            $fragen07 + $fragen08 + $fragen09 + $fragen10 + $fragen11;
    }
    $_SESSION['sc_test_fragen'] = $fragen;
    $_SESSION['pruefling'] = $pruefling;
}

$timelimit = 45*60; // in Sekunden

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = 'Basis-Schiritest der Deutschen Einradhockeyliga';
include '../../templates/header.tmp.php'; # Html-header und Navigation

if ($pruefling==''){
    echo '<H4><form method="post">' .
        '<input type="submit" class="w3-btn w3-block w3-pale-red"' .
        ' value="Neuen Übungstest erzeugen"></form></H4>';
}
# Start Debug Modus
if (isset($DEBUGMODUS)) {
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
    echo '<P>Danke für das Ausfüllen des Schiritests, deine Antworten sind an den ';
    echo 'Ligaausschuss geschickt worden.';
    if ($pruefling == '') {
        $text_bestanden = ' Herzlichen Glückwunsch, du hast bestanden! ';
        $text_durchgefallen = ' Du hast leider nicht bestanden. ';
    } else {
        $text_bestanden = ' Herzlichen Glückwunsch, ' . $pruefling . ', du hast bestanden! ';
        $text_durchgefallen = ' Du hast leider nicht bestanden, ' . $pruefling . '. ';
    }
    if ($richtig >= 25) { # bestanden:
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
    echo '<H2>' . $titel . '</H2>';
    echo '<UL><LI>Der Test besteht aus ' . count($fragen) . ' Fragen.</LI>';
    echo '<LI>Es können mehrere Antwortmöglichkeiten richtig sein.</LI>';
    echo '<LI>Mindestens 1 Antwort ist immer richtig.</LI>';
    echo '<LI>Du hast 45 Minuten Zeit.</LI></UL>';
    ?>
    <div class="w3-center w3-white w3-bottombar w3-border-primary"
         style="position: sticky; top: 0; z-index: 1000;">
        <?php
        Html::countdown(time() + $timelimit);
        ?>
    </div>
    <?php
}

echo '<form method="post">';
$frage_index = 0;
foreach ($fragen as $frage_id => $frage) { # Schleife über alle Fragen:
    echo '<div class="w3-section w3-display-container">';
    $frage_index++;
    SchiriTest::frage_anzeigen($frage_id, $frage_index, $frage);
    if (isset($_POST['beantworten'])) { # Test auswerten:
        SchiriTest::auswertung_anzeigen($frage_id, $frage);
    } else { # Test anzeigen:
        SchiriTest::antworten_anzeigen($frage_id, $frage);
    }
    echo '</div>';
    if (isset($DEBUGMODUS)) { # Start Debug Modus
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
    ?>
    <h3 class="w3-topbar">Fertig!</h3>
    <P>Du kannst dir alle Fragen nochmals ansehen, und du kannst deine
        Antworten jetzt noch ändern. Dann bitte auf "Test abgeben" klicken,
        danach sind keine Änderungen mehr möglich.</P>
    <button type="submit" class="w3-button w3-block w3-primary" name="beantworten">
        <i class="material-icons">check_circle_outline</i> Test abgeben
    </button>
<?php } # endif
echo '</form>';
include '../../templates/footer.tmp.php';
