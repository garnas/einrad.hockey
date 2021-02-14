<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

//Formularauswertung
if(isset($_POST['login'])) {
    $log_file = "log_login.log";
    $login_name = $_POST['loginname'];
    $la_id = Ligaleitung::get_la_id($login_name);
    $passwort = $_POST['passwort'];

    //Fehlermeldungen
    if(strlen($login_name) == 0) {
        $error = true;
        Form::error("Bitte Login eingeben");
    }
    if(strlen($passwort) == 0 or strlen($passwort) > 100) {
        $error = true;
        Form::error("Bitte Passwort eingeben");
    }
    if (empty($la_id)){
        $error = true;
        Form::error("Unbekannter Loginname");
        Form::log($log_file, "Falscher LC-Login | Loginname: " . $login_name);
        unset ($_POST['loginname']); //Damit der Name als Value im Input-Feld gelöscht wird.
    }

    //Passwortcheck
    if (!($error ?? false)){
        if(password_verify($passwort, Ligaleitung::get_la_password($la_id))) {
            $_SESSION['la_login_name'] = $login_name;
            $_SESSION['la_id'] = $la_id;
            //Logdatei erstellen/beschreiben
            Form::log($log_file, "Erfolgreich       | Loginname: " . $_SESSION['la_login_name']);
            //Weiterleitung zum in der Session (aus session.logic.php) gespeicherten Pfad oder zu start.php
            //Wegen header-injection sollten keine Pfade an den header via Get übergeben werden
            if(isset($_GET['redirect']) && isset($_SESSION['lc_redirect'])){
                $redirect = $_SESSION['lc_redirect'];
                unset($_SESSION['lc_redirect']);
            }else{
                $redirect = 'lc_start.php';
            }
            header('Location: ' . $redirect);
            die();
        }else{
            //Logdatei erstellen/beschreiben
            Form::log($log_file, "Falsches Passwort | Loginname: " . $login_name);
            Form::error("Falsches Passwort");
        }
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$page_width = "480px";
Config::$titel = "Ligacenter | Deutsche Einradhockeyliga";
Config::$content =
    "Im Ligacenter kann der Ligaausschuss die Liga verwalten. Nur Mitglieder des Ligaausschusses haben einen Login.";
include '../../templates/header.tmp.php';
?>

<form method="post" class="w3-card-4 w3-panel">
    <h1 class="w3-text-primary">Ligacenter</h1>
    <p class="w3-text-grey">
        Im Ligacenter kann der Ligaausschuss die Liga verwalten. Nur Mitglieder des Ligaausschusses haben einen Login.
    </p>
    <div onclick='document.getElementById("login").value = "";document.getElementById("passwort").value = "";'
         class="no w3-right w3-text-red w3-hover-text-secondary" style="cursor: pointer;">
        <i class="material-icons">clear</i>
    </div>
        <label for="login"><i class="material-icons">account_circle</i> Login:</label>
        <input class="w3-input w3-border-primary"
               value="<?=$_POST['loginname'] ?? ''?>"
               type="text"
               id="login"
               name="loginname"
               required
        >
    <p>
        <label for="passwort"><i class="material-icons">lock</i> Passwort:</label>
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

<?php include '../../templates/footer.tmp.php';?>