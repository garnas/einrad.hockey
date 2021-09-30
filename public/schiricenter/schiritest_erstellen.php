<?php # -*- php -*-

require_once '../../init.php'; # Autoloader und Session, muss immer geladen werden!
#qqq require_once '../../logic/session_la.logic.php'; # Auth
require_once '../../logic/la_spieler_waehlen.logic.php';

$titel = 'Schiritest erstellen';

if (isset($_POST['reset'])) {
    Helper::reload();
}

if (isset($_GET['spieler_id'])) {
    $spieler = nSpieler::get((int)$_GET['spieler_id']);
    if  (!isset($spieler->spieler_id)) {
        Html::error("Spieler wurde nicht gefunden");
    }
}

##############################################################################

Html::$titel = $titel;
include Env::BASE_PATH . '/templates/header.tmp.php';
echo '<h1 class="w3-text-primary">' . $titel . '</h1>'; 

if (!isset($_POST['create'])) {
    if (!isset($spieler->spieler_id)) {
        echo '<H3>Schritt 1: Prüfling auswählen</H3>';
        include Env::BASE_PATH . '/templates/la_spieler_waehlen.tmp.php';
    } else {
        echo '<H3>Schritt 2: Level und E-Mail-Adresse angeben</H3>'; ?>
        <div class="w3-panel w3-card-4">
            <h4>Prüfling: <?= $spieler->get_name() ?></h4>
            <h4>Team: <?= $spieler->get_team() ?></h4>
            <form method="post">
                <h4 class="w3-text-primary">
                    <label for="test_level">Level wählen:</label></h4>
                <select required id="test_level" name="test_level"
                    class="w3-select w3-border w3-border-primary">
                    <option value="J">Juniortest</option>
                    <option value="B" selected>Basistest</option>
                    <option value="F">Fortgeschritten</option>
                </select>
                <h4 class="w3-text-primary">
                    <label for="email">E-Mail-Adresse des Prüflings:</label></h4>
                <input required type="email" name="email" id="email"
                    class="w3-input w3-border w3-border-primary">
                <p>
                <button type="submit" class="w3-button w3-tertiary"
                    name="create">Jetzt Schiritest erstellen und Prüfling per
                        E-Mail informieren</button>
                </p>
            </form>
        </div>
    <?php }
} else {
    $errortext ='';
    $email = $_POST['email'];
    $test_level = $_POST['test_level'];
    $test = new SchiriTest();
    $test->set_spieler($spieler)
        ->set_level($test_level)
        ->set_email($email)
        ->set_pruefungs_fragen();
    if (!$test->create()) {
        Html::message('error', 'Test für ' . $test->spieler->get_name() .
            ' konnte nicht erstellt werden.', 'Fehler:');
    } else { ?>
        <H3>Der Test wurde erfolgreich erstellt und in die Datenbank eingetragen.</H3>
        <table>
        <tr><td>Zeitstempel: </td><td><?= $test->zeitstempel ?>         </td></tr>
        <tr><td>Prüfling:    </td><td><?= $test->spieler->get_name() ?> </td></tr>
        <tr><td>E-Mail:      </td><td><?= $test->email ?>               </td></tr>
        <tr><td>Team:        </td><td><?= $test->spieler->get_team() ?> </td></tr>
        <tr><td>Level:       </td><td><?= $test->test_level ?>          </td></tr>
        <tr><td>URL:         </td><td><?= $test->url ?>                 </td></tr>
        <tr><td>Fragen-IDs:  </td><td><?= $test->gestellte_fragen ?>    </td></tr>
        </table>
        <?php
        if (!$test->mail_on_create()) {
            Html::message('error', 'E-Mail an ' . $test->spieler->get_name() .
                ' konnte nicht versendet werden.', 'Fehler:');
        } else { 
            echo '<H4>' . $test->spieler->get_name() . ' &lt;' . $test->email . '&gt';
            echo ' hat eine E-Mail mit dem Link zur Prüfung bekommen.</H4>';
        }
    }
}

echo '<P><form method="post">';
echo '<button type="submit" class="w3-button w3-hover-indigo w3-block w3-primary"';
echo 'name="reset">(Formular zurücksetzen, neuen Test erstellen)</button>';
echo '</form></P>';

include Env::BASE_PATH . '/templates/footer.tmp.php';
