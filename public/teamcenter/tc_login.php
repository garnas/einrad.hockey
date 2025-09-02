<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Service\Team\TeamService;

require_once '../../init.php';
Helper::ensure_no_request_logging();

// Formularauswertung
if (isset($_POST['login'])) {
    $teamname = $_POST['teamname'] ?? '';
    $passwort = $_POST['passwort'] ?? '';

    // Fehlermeldungen
    if (empty($teamname) || empty($passwort)) {
        $error = true;
        Html::error("Bitte Logins ausfüllen.");
    }

    // Passwort überprüfen
    if (TeamService::login($teamname, $passwort)) {
        if (isset($_SESSION['tc_redirect'], $_GET['redirect'])) {
            $redirect = $_SESSION['tc_redirect'];
            unset($_SESSION['tc_redirect']);
        } else {
            $redirect = 'tc_start.php';
        }
        header('Location: ' . $redirect);
        die();
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$page_width = "480px";
Html::$titel = "Teamcenter | Deutsche Einradhockeyliga";
Html::$content =
    "Im Teamcenter können Teams ihren Kader verwalten, ihre Teamdaten ändern, Emails versenden und sich zu Turnieren an-
     und abmelden.";
include '../../templates/header.tmp.php'; ?>

    <form method="post" class="w3-card-4 w3-panel ">
        <h1 class="w3-text-primary">Teamcenter</h1>
        <p class="w3-text-grey">Im Teamcenter können Teams ihren Kader verwalten, ihre Teamdaten ändern und sich zu
                                Turnieren an- und abmelden.</p>
        <div onclick='document.getElementById("teamname").value = "";document.getElementById("passwort").value = "";'
             class="no w3-right w3-text-red w3-hover-text-secondary" style="cursor: pointer;">
            <i class="material-icons">clear</i>
        </div>
        <label for="teamname">
            <i class="material-icons">group</i>
            Team:</label>
        <input class="w3-input w3-border-primary"
               value="<?= $_POST['teamname'] ?? '' ?>"
               placeholder="Team"
               type="text"
               list="teams"
               id="teamname"
               name="teamname"
               required
        >
        <?= Html::datalist_teams() ?>
        <p>
            <label for="passwort">
                <?=Html::icon("account_circle")?> Passwort:</label>
            <input class="w3-input w3-border-primary"
                   type="password"
                   size="30"
                   maxlength="200"
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

<?php include '../../templates/footer.tmp.php';