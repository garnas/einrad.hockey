<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; // Autoloader und Session, muss immer geladen werden!
# require_once '../../logic/session_team.logic.php'; // Nur im Teamcenter zugreifbar

// Antwort auswerten oder neue Frage stellen?
if (isset($_POST['beantworten'])){
    $fragen = $_SESSION['sc_test_fragen'];
}else{
    if (isset($_POST['ausgewaehlte_nummer'])){
        $ausgewaehlte_nummer = $_POST['ausgewaehlte_nummer'];
    }else{
        $ausgewaehlte_nummer = '42';
    }
    if (ctype_digit($ausgewaehlte_nummer)){ // Input enthält nur Ziffern
        // eingetippte Nummer auswählen:
        $fragen = SchiriTest::get_fragen('*', 1, $ausgewaehlte_nummer);
    }elseif (isset($_POST['neue_fragen'])){
        // zufällige Frage auswählen:
        $fragen = SchiriTest::get_fragen('*', 1);
    }else{
        // nächste Frage (n+1) auswählen:
        $fragen = SchiriTest::get_fragen('*', 1, $_SESSION['frage_id']+1);
    }
    $_SESSION['sc_test_fragen'] = $fragen;
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
?>


<?php
Config::$titel = 'Multiple-Choice Schiritest der Deutschen Einradhockeyliga';
include '../../templates/header.tmp.php'; // Html-header und Navigation
?>

<!-- Nach unten verschoben, da kleine Probleme mit den Headern (Ansgar) -->
<!-- Start Debug Modus -->
<form method="post">
    <div class='w3-card w3-panel w3-leftbar w3-border-red w3-pale-red'>
        Frage Nr.:
        <input type="text" size="3" name="ausgewaehlte_nummer">
        <input type="submit" value="auswählen">
        (leere Eingabe erhöht Fragennummer um 1)
    </div>
</form>

<!-- Ende Debug Modus -->
<form method="post">
    <?php foreach ($fragen as $frage_id => $frage) { ?>
        <!-- Einzelne Frage -->
        <div class="w3-section w3-display-container">
            <?php SchiriTest::frage_anzeigen($frage_id, $frage); ?>
            <?php if (!isset($_POST['beantworten'])){?>
                <?php foreach ($frage['antworten'] as $index => $antwort){ ?>
                    <p>
                        <!-- Input als Array -->
                        <input name="abgabe[<?= $frage_id ?>][<?= $index ?>]"
                               value="<?= $index ?>"
                               id="<?= $frage_id . '*' . $index ?>"
                               type="checkbox"
                               class="w3-check"
                               style="cursor: pointer;">
                        <label for="<?= $frage_id . '*' . $index ?>"
                               class="w3-hover-text-primary" style="cursor: pointer;">
                            <?= $antwort ?>
                        </label>
                    </p>
                <?php } //end foreach antworten?>
            <?php }else{?>
                <?php $richtig = SchiriTest::get_richtig($frage_id) ?>
                <?php foreach ($frage['antworten'] as $index => $antwort){ ?>
                    <p>
                        <?php $antwort_user = isset($_POST['abgabe'][$frage_id][$index]); ?>
                        <?php if ($antwort_user){ ?>
                            <i class="material-icons">check_circle_outline</i>
                        <?php }else{?>
                            <i class="material-icons">radio_button_unchecked</i>
                        <?php } //endif?>
                        <?php $antwort_richtig = in_array($index, $richtig);
                        if ($antwort_user xor $antwort_richtig){ ?>
                            <span class="w3-text-red"><i class="material-icons">thumb_down</i></span>
                        <?php }else{?>
                            <span class="w3-text-green"><i class="material-icons">thumb_up</i></span>
                        <?php } //endif?>
                        <?php if ($antwort_richtig) { ?>
                            <b><?= $antwort ?></b>
                        <?php }else{?>
                            <span class="w3-text-grey"><s><i><?= $antwort ?></i></s></span>
                        <?php } //endif?>
                    </p>
                <?php } //end foreach antworten?>
                <?php $antworten_user = $_POST['abgabe'][$frage_id] ?? []; // Leer, falls keine Antwort abgegeben. ?> 
                <?php if (SchiriTest::validate_frage($frage_id, $antworten_user)){?>
                    <h3 class="w3-bottombar">Alles korrekt beantwortet!
                        <span class="w3-text-green"><i class="material-icons">thumb_up</i></span></h3>
                <?php }else{?>
                    <h3 class="w3-bottombar">Da war etwas falsch!
                        <span class="w3-text-red"><i class="material-icons">thumb_down</i></span></h3>
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
                    Form::message('notice', '(Keine Regelnummer für diese Frage)');
                }else{
                    foreach (preg_split('/[\s#\s]+/', $regelnr) as $regelnr1){
                        list($nr, $part, $titel, $text) = SchiriTest::get_regel($regelnr1);
                        if ($nr == ''){
                            Form::message('error',
                                'Regel |' . $regelnr1 . '| nicht in der Datenbank.');
                        }else{
                            Form::message('info', $text,
                                "Offizielle Regel " . $nr . ": " . $titel, esc:false);
                        }
                    }
                }                    
                ?>
                
            <?php } //endif?>

        </div>
    <?php } //end foreach fragen?>
    <?php $_SESSION['frage_id'] = $frage_id; // Fragennummer abspeichern ?>
    <p>
        <?php if (isset($_POST['beantworten'])){?>
            <button type="submit" class="w3-button w3-block w3-primary" name="neue_fragen">
                <i class="material-icons">cached</i> Nächste Frage
            </button>
        <?php }else{?>
            <button type="submit" class="w3-button w3-block w3-primary" name="beantworten">
                <i class="material-icons">check_circle_outline</i> Frage beantworten
            </button>
        <?php } //endif?>
    </p>
</form>

<?php
include '../../templates/footer.tmp.php';

// Start Debug Modus 
$debuginfo  = "Kategorie:     " . $frage['kategorie'];
$debuginfo .= "<BR>LJBF:          " . $frage['LJBF'];
$debuginfo .= "<BR>richtig:       ";
foreach($frage['richtig'] as $i) {
    $debuginfo .= $i . " ";
}
$debuginfo .= "<BR>Regelnummer:   " . $frage['regelnr'];
$debuginfo .= "<BR>Punkte:        " . $frage['punkte'];
$debuginfo .= "<BR>bestätigt:     " . $frage['bestaetigt'];
$debuginfo .= "<BR>interne Notiz: " . $frage['interne_notiz'];
Form::message('error', $debuginfo, "Infos zu Frage Nr. " . $frage_id . " (Debug Modus):",
              esc:false);
// Ende Debug Modus 
?>
