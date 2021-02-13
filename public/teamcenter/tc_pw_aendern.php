<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; // autoloader und Session

$no_redirect = true; // Verhindert die Endlosschleife, bei der Überprüfung, ob das Passwort geändert wurde
require_once '../../logic/session_team.logic.php'; // Auth

$team = new Team ($_SESSION['team_id']);

//Formularauswertung
if(isset($_POST['change'])) {
    $passwort_alt = $_POST['passwort_alt'];
    $passwort_neu = $_POST['passwort_neu'];

    if (strlen($passwort_neu) >= 6 && strlen($passwort_neu) < 100){ // Zu lange Passwörter sollten ebenfalls verhindert werden, damit keine Fehlermeldungen provoziert werden können
        if(password_verify($passwort_alt, $team->get_passwort())) {
            $team->set_passwort($passwort_neu);
            Form::affirm("Euer Passwort wurde geändert.");
            header('Location: tc_start.php');
            die();
        }else{
            Form::error("Falsches altes Passwort");
        }
    }else{
        Form::error("Ungültiges neues Passwort. (Euer Passwort muss mindestens 6 Zeichen lang sein)");
    }
}

Form::attention("Euer Passwort wird verschlüsselt gespeichert. Es muss mindestens sechs Zeichen lang sein.");

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$page_width = "480px";
include '../../templates/header.tmp.php';
?>

<form method="post" class="w3-panel w3-card-4">
    <h3 class="w3-text-primary"> Teamcenter-Passwort ändern </h3>
    <label class="w3-text-grey" for="passwort_alt">Altes Passwort:</label>
    <input required class="w3-input w3-border w3-border-primary" type="password" id="passwort_alt" name="passwort_alt">
    <p>
    <label class="w3-text-grey" for="passwort_neu">Neues Passwort:</label>
    <input required class="w3-input w3-border w3-border-primary" type="password" id="passwort_neu" autocomplete="new-password" name="passwort_neu">
    <p>
    <input class="w3-button w3-tertiary" type="submit" name="change" value="Passwort ändern">
    </p>
</form>

<?php include '../../templates/footer.tmp.php';?>