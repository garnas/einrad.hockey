<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

// Formularauswertung
if (isset($_POST['login'])) {
    $login = $_POST['loginname'] ?? '';
    $passwort = $_POST['passwort'] ?? '';
    $error = false;

    if (empty($login) || empty($passwort)) {
        $error = true;
        Form::error("Bitte beide Felder ausfüllen");
    }
    // Login Check
    if (!$error && Ligaleitung::login($login, $passwort, 'ligaausschuss')) {
        if (isset($_SESSION['lc_redirect'], $_GET['redirect'])) {
            $redirect = $_SESSION['lc_redirect'];
            unset($_SESSION['lc_redirect']);
        } else {
            $redirect = 'lc_start.php';
        }
        header('Location: ' . $redirect);
        die();
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$page_width = "480px";
Config::$titel = "Ligacenter | Deutsche Einradhockeyliga";
Config::$content =
    "Im Ligacenter kann der Ligaausschuss die Liga verwalten. Nur Mitglieder des Ligaausschusses haben einen Login.";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

    <form method="post" class="w3-card-4 w3-panel">
        <h1 class="w3-text-primary">Ligacenter</h1>
        <p class="w3-text-grey">
            Im Ligacenter kann der Ligaausschuss die Liga verwalten. Nur Mitglieder des Ligaausschusses haben einen
            Login.
        </p>
        <div onclick='document.getElementById("login").value = "";document.getElementById("passwort").value = "";'
             class="no w3-right w3-text-red w3-hover-text-secondary" style="cursor: pointer;">
            <i class="material-icons">clear</i>
        </div>
        <label for="login">
            <?=Form::icon("account_circle")?> Login
        </label>
        <input class="w3-input w3-border-primary"
               value="<?= $_POST['loginname'] ?? '' ?>"
               type="text"
               id="login"
               name="loginname"
               required
        >
        <p>
            <label for="passwort">
                <?=Form::icon("lock")?> Passwort
            </label>
            <input class="w3-input w3-border-primary"
                   type="password"
                   id="passwort"
                   name="passwort"
                   required
            >
        </p>
        <p>
            <button class="w3-button w3-ripple w3-round w3-tertiary"
                    type="submit"
                    name="login"
            >
                <?= Form::icon("login") ?> Login
            </button>
        </p>
    </form>

<?php include Env::BASE_PATH . '\templates\footer.tmp.php';