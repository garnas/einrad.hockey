<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../init.php';
Helper::ensure_no_request_logging();

if (LigaLeitung::is_logged_in("ligaausschuss") || LigaLeitung::is_logged_in("admin")) {
    Helper::reload("/ligacenter/lc_start.php");
}
if (LigaLeitung::is_logged_in("team_social_media")) {
    Helper::reload("/oefficenter/oc_start.php");
}

// Formularauswertung
if (isset($_POST['login'])) {
    $login = $_POST['loginname'] ?? '';
    $passwort = $_POST['passwort'] ?? '';
    $error = false;

    if (empty($login) || empty($passwort)) {
        $error = true;
        Html::error("Bitte beide Felder ausfüllen");
    }
    
    // Login Check
    if (!$error && LigaLeitung::login($login, $passwort, 'ligaausschuss')) {
        if (isset($_SESSION['lc_redirect'], $_GET['redirect'])) {
            $redirect = $_SESSION['lc_redirect'];
            unset($_SESSION['lc_redirect']);
        } else {
            $redirect = '/ligacenter/lc_start.php';
        }
        Helper::reload($redirect);
    }

    if (!$error && LigaLeitung::login($login, $passwort, 'team_social_media')) {
        if (isset($_SESSION['oc_redirect'], $_GET['redirect'])) {
            $redirect = $_SESSION['oc_redirect'];
            unset($_SESSION['oc_redirect']);
        } else {
            $redirect = '/oefficenter/oc_start.php';
        }
        Helper::reload($redirect);
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$page_width = "480px";
Html::$titel = "Login Ligaleitung | Deutsche Einradhockeyliga";
Html::$content = "Hier können sich Ausschüsse einloggen, um die Liga zu verwalten.";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

    <form method="post" class="w3-card-4 w3-panel">
        <h1 class="w3-text-primary">Login Ligaleitung</h1>
        <p class="w3-text-grey">
            Hier können sich Ausschüsse einloggen, um die Liga zu verwalten.
        </p>
        <div onclick='document.getElementById("login").value = "";document.getElementById("passwort").value = "";'
             class="no w3-right w3-text-red w3-hover-text-secondary" style="cursor: pointer;">
            <i class="material-icons">clear</i>
        </div>
        <label for="login">
            <?=Html::icon("account_circle")?> Login
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
                <?=Html::icon("lock")?> Passwort
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
                <?= Html::icon("login") ?> Login
            </button>
        </p>
    </form>

<?php include Env::BASE_PATH . '/templates/footer.tmp.php';