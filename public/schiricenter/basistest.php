<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php'; // Autoloader und Session, muss immer geladen werden!
# require_once '../../logic/session_team.logic.php'; // Nur im Teamcenter zugreifbar

// Antwort auswerten oder neue Frage stellen?
if (isset($_POST['beantworten'])){
    $fragen = $_SESSION['sc_test_fragen'];
    $gesamt = 0;
    $richtig = 0;
    foreach ($fragen as $frage_id => $frage) {
        $gesamt += 1;
        $antworten_user = $_POST['abgabe'][$frage_id] ?? [];
        if (SchiriTest::validate_frage($frage_id, $antworten_user)){
            $richtig += 1;    
        }
    }
}else{
    $fragen01 = SchiriTest::get_fragen('1',  2); // 16 Vor dem Spiel / Rund ums Spiel 
    $fragen02 = SchiriTest::get_fragen('2',  3); // 27 Schiedsrichterverhalten        
    $fragen03 = SchiriTest::get_fragen('3',  1); // 13 Handzeichen                    
    $fragen04 = SchiriTest::get_fragen('4',  1); // 13 Penaltyschießen                
    $fragen05 = SchiriTest::get_fragen('5',  3); //  8 Vorfahrt                       
    $fragen06 = SchiriTest::get_fragen('6',  3); //  5 Übertriebene Härte             
    $fragen07 = SchiriTest::get_fragen('7',  3); // 18 Eingriff ins Spiel             
    $fragen08 = SchiriTest::get_fragen('8',  6); // 35 Sonstige Fouls                 
    $fragen09 = SchiriTest::get_fragen('9',  4); // 16 Torschüsse                     
    $fragen10 = SchiriTest::get_fragen('10', 1); // 16 Zeitstrafen / Unsportlichkeiten
    $fragen11 = SchiriTest::get_fragen('11', 3); // 22 Strafen                        
    $fragen = $fragen01 + $fragen02 + $fragen03 + $fragen04 + $fragen05 + $fragen06 +
              $fragen07 + $fragen08 + $fragen09 + $fragen10 + $fragen11;
    $_SESSION['sc_test_fragen'] = $fragen;
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = 'Multiple-Choice Basis Schiritest der Deutschen Einradhockeyliga';
include '../../templates/header.tmp.php'; // Html-header und Navigation
?>

<!-- Start Debug Modus -->
<H4><form method="post">
    <input type="submit" class='w3-btn w3-block w3-pale-red'
           value="DEBUG MODUS: Neuen Test erzeugen">
</form></H4>
<!-- Ende Debug Modus -->
<?php if (isset($_POST['beantworten'])){?>
    <H2>Ergebnis: Du hast <?= $richtig ?> von <?= $gesamt ?>
        Fragen richtig beantwortet.</H2>
    Danke für das Ausfüllen des Schiritests, deine Antworten sind an den
    Ligaausschuss geschickt worden. Hier ist eine ausführliche Auswertung.
    <UL>
        <LI>Deine Antworten werden mit einem Häkchen im Kreis angezeigt.</LI>
        <LI>Der grüne bzw. rote Daumen zeigt, ob deine Antwort stimmt.</LI>
        <LI>Die richtigen Antworten sind jetzt fett gedruckt, die falschen
            sind grau und durchgestrichen.</LI>
        <LI>Die entsprechende Regel wird in einem grünen Kasten angezeigt. Bei
            manchen Fragen gibt es auch noch eine zusätzliche Erklärung.</LI>
    </UL>
<?php }else{?>
    <H2>Multiple-Choice Basis Schiritest</H2>
    <UL>
        <LI>Der Test besteht aus 30 Fragen.</LI>
        <LI>Es können mehrere Antwortmöglichkeiten richtig sein.</LI>
        <LI>Mindestens 1 Antwort ist immer richtig.</LI>
        <LI>Du hast 45 Minuten Zeit.</LI>
    </UL>
<?php } //endif?>

<form method="post">
    <?php $frage_index = 0;
    foreach ($fragen as $frage_id => $frage) { ?>
        <!-- Einzelne Frage -->
        <div class="w3-section w3-display-container">
            <?php $frage_index++;
            SchiriTest::frage_anzeigen($frage_index, $frage);
            if (!isset($_POST['beantworten'])){
                SchiriTest::antworten_anzeigen($frage_id, $frage);
            }else{?>
                <?php $richtig = SchiriTest::get_richtig($frage_id);
                foreach ($frage['antworten'] as $index => $antwort){ ?>
                    <p>
                        <?php $antwort_user = isset($_POST['abgabe'][$frage_id][$index]);
                        if ($antwort_user){ ?>
                            <i class="material-icons">check_circle_outline</i>
                        <?php }else{?>
                            <i class="material-icons">radio_button_unchecked</i>
                        <?php } //endif
                        $antwort_richtig = in_array($index, $richtig);
                        if ($antwort_user xor $antwort_richtig){ ?>
                            <span class="w3-text-red"><i class="material-icons">thumb_down</i></span>
                        <?php }else{?>
                            <span class="w3-text-green"><i class="material-icons">thumb_up</i></span>
                        <?php } //endif
                        if ($antwort_richtig) { ?>
                            <b><?= $antwort ?></b>
                        <?php }else{?>
                            <span class="w3-text-grey"><s><i><?= $antwort ?></i></s></span>
                        <?php } //endif?>
                    </p>
                <?php } //end foreach antworten
                $antworten_user = $_POST['abgabe'][$frage_id] ?? []; // Leer, falls keine Antwort abgegeben. ?>
                <?php if (SchiriTest::validate_frage($frage_id, $antworten_user)){?>
                    <h3 class="w3-border-bottom">
                        <span size="50" class="w3-text-green">
                            <i class="material-icons md-36">thumb_up</i>
                            Alles korrekt beantwortet!</span></h3>
                <?php }else{?>
                    <h3 class="w3-border-bottom">
                        <span class="w3-text-red">
                            <i class="material-icons md-36">thumb_down</i>
                            Da war etwas falsch!</span></h3>
                <?php } //endif?>

                <p><b>Erklärung:</b> <?= $fragen[$frage_id]['erklaerung'] ?></p>
                <?php if(!empty($frage['erklaerung_video'])){?>
                    <!-- Video zur Frage -->
                    <div style="max-width: 500px"> <!-- Damit das Video nicht zu groß wird -->
                        <video class="w3-image w3-card" src="videos/<?=$frage['erklaerung_video']?>"
                               controls playsinline > Video zur Frage
                        </video>
                    </div>
                <?php } //endif?>
                <?php if(!empty($frage['erklaerung_bild'])){?>
                    <!-- Bild zur Frage -->
                    <div style="max-width: 500px"> <!-- Damit das Bild nicht zu groß wird -->
                        <img alt="Bild zur Frage" class="w3-image w3-card"
                             src="bilder/<?=$frage['erklaerung_bild']?>">
                    </div>
                <?php } //endif?>

                <?php
                $regelnr = $fragen[$frage_id]['regelnr'];
                if ($regelnr == ''){
                    Html::message('notice', '(Keine Regelnummer für diese Frage)');
                }else{
                    foreach (preg_split('/[\s#\s]+/', $regelnr) as $regelnr1){
                        list($nr, $part, $titel, $text) = SchiriTest::get_regel($regelnr1);
                        if ($nr == ''){
                            Html::message(
                                'error', 'Regel |' . $regelnr1 . '| nicht in der Datenbank.');
                        }else{
                            Html::message(
                                'info', $text, "Offizielle Regel " .
                                               $nr . ": " . $titel, esc:false);
                        }
                    }
                }
                ?>

            <?php } //endif?>

        </div>
        <!-- Start Debug Modus -->
        <?php if (isset($DEBUGMODUS)){
            $debuginfo  = "frage_id:      " . $frage_id;
            $debuginfo .= "<BR>Kategorie: " . $frage['kategorie'];
            $debuginfo .= "<BR>LJBF:      " . $frage['LJBF'];
            $debuginfo .= "<BR>richtig:   ";
            foreach($frage['richtig'] as $i) {$debuginfo .= $i . " ";}
            $debuginfo .= "<BR>Regelnummer:   " . $frage['regelnr'];
            $debuginfo .= "<BR>interne Notiz: " . $frage['interne_notiz']; ?>
            <p class='w3-block w3-pale-red'><?= $debuginfo ?></p>
            <?php } ?><!-- Ende Debug Modus -->
    <?php } //end foreach fragen
    $_SESSION['frage_id'] = $frage_id; // Fragennummer abspeichern
    if (! isset($_POST['beantworten'])){?>
        <h3 class="w3-topbar">Fertig!</h3>
        <P>Du kannst dir alle Fragen nochmals ansehen, und du kannst deine
           Antworten jetzt noch ändern. Dann bitte auf "Test abgeben" klicken,
           danach sind keine Änderungen mehr möglich.</P>
        <button type="submit" class="w3-button w3-block w3-primary" name="beantworten">
            <i class="material-icons">check_circle_outline</i> Test abgeben
        </button>
    <?php } //endif?>
</form>

<?php
include '../../templates/footer.tmp.php';
?>
