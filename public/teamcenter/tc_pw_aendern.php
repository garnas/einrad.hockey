<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; // autoloader und Session
Config::$teamcenter_no_redirect = true; // Verhindert die Endlosschleife, bei der Überprüfung, ob Passwort erneurt wurde
require_once '../../logic/session_team.logic.php'; // Auth

// Formularauswertung
if(isset($_POST['change'])) {

    $passwort_alt = $_POST['passwort_alt'] ?? '';
    $passwort_neu = $_POST['passwort_neu'] ?? '';
    $error = false;

    if (empty($passwort_neu) || empty($passwort_alt)) {
        $error = true;
        Html::error("Bitte beide Felder ausfüllen");
    }

    if (strlen($passwort_neu) < 6) {
        $error = true;
        Html::error("Euer neues Passwort muss mindestens 6 Zeichen lang sein");
    }

    if(!password_verify($passwort_alt, $team->details['passwort'])) {
        $error = true;
        Html::error("Falsches altes Passwort");
    }

    if(!$error){
        $team->set_passwort($passwort_neu);
        Html::info("Euer Passwort wurde geändert.");
        header('Location: tc_start.php');
        die();
    }
}

Html::notice("Euer Passwort wird verschlüsselt gespeichert. Es muss mindestens sechs Zeichen lang sein.");

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$page_width = "480px";
include '../../templates/header.tmp.php';
?>

<form method="post" class="w3-panel w3-card-4">
    <h3 class="w3-text-primary"> Teamcenter-Passwort ändern </h3>
    <label class="w3-text-grey" for="passwort_alt"><?= Html::icon('remove') ?> Altes Passwort:</label>
    <input required
           class="w3-input w3-border w3-border-primary"
           type="password"
           id="passwort_alt"
           name="passwort_alt"
    >
    <p>
    <label class="w3-text-grey" for="passwort_neu"><?= Html::icon('add') ?> Neues Passwort:</label>
    <input required
           class="w3-input w3-border w3-border-primary"
           type="password"
           id="passwort_neu"
           autocomplete="new-password"
           name="passwort_neu"
    >
    <p>
        <button class="w3-button w3-tertiary" type="submit" name="change">
            <?= Html::icon('create') ?> Passwort ändern
        </button>
    </p>
</form>

<?php include '../../templates/footer.tmp.php';?>