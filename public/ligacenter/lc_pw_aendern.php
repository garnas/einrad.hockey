<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

//Formularauswertung
if(isset($_POST['change'])) {
    $passwort_alt = $_POST['passwort_alt'];
    $passwort_neu = $_POST['passwort_neu'];
    if (strlen($passwort_neu) > 8){
        if(LigaLeitung::set_passwort($_SESSION['logins']['la']['login'], $passwort_neu, $passwort_alt)) {
            Html::info("Dein Passwort wurde geändert");
            header('Location: lc_start.php');
            die();
        }
        Html::error("Falsches Passwort");
    }else{
        Html::error("Das Passwort muss mindestens acht Zeichen lang sein.");
    }    
}



/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$page_width = "500px";
include '../../templates/header.tmp.php'; ?>

<form method="post" class="w3-panel w3-card-4">
    <h3> Ligacenter-Passwort ändern </h3>
        <label class="w3-text-primary" for="passwort_alt">Altes Passwort:</label>
        <input required
               class="w3-input w3-border w3-border-primary"
               type="password"
               id="passwort_alt"
               name="passwort_alt">
    <p>
        <label class="w3-text-primary" for="passwort_neu">Neues Passwort:</label>
        <input required
               class="w3-input w3-border w3-border-primary"
               type="password"
               id="passwort_neu"
               autocomplete="new-password"
               name="passwort_neu">
    <p>
        <input class="w3-button w3-tertiary" type="submit" name="change" value="Passwort ändern">
    </p>
</form>

<?php include '../../templates/footer.tmp.php';