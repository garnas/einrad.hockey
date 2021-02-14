<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

//Formularauswertung
if(isset($_POST['login'])) {
    $teamname = $_POST['teamname'];
    $passwort = $_POST['passwort'];
    $team_id = (int) Team::teamname_to_teamid ($teamname);
    $log_file = "log_login.log";
    
    //Fehlermeldungen
    if(strlen($teamname) == 0) {
        $error = true;
        Form::error("Bitte Teamname eingeben");
    }
    if(strlen($passwort) == 0) {
        $error = true;
        Form::error("Bitte Passwort eingeben");
    }
    if (!Team::is_ligateam($team_id)){
        $error = true;
        Form::error("Falscher Loginname");
        Form::log($log_file, "Falscher Teamname | Teamname: " . $_POST['teamname'] ?? '');
        unset ($_POST['teamname']); //Damit der Teamname als Value im Input-Feld gelöscht wird.
    }

    //Passwort überprüfen
    if(!($error ?? false)) {
        $team = new Team($team_id);
        if(password_verify($passwort, $team->get_passwort())) {
            $_SESSION['team_id'] = $team->id;
            $_SESSION['teamname'] = Team::teamid_to_teamname($team->id); // Da SQL Groß- / kleinschreibung ignoriert.
            $_SESSION['teamblock'] = Tabelle::get_team_block($team->id);
            // Logdatei erstellen/beschreiben
            Form::log($log_file, "Erfolgreich       | Teamname: " . $teamname);
                // Weiterleitung zum in der Session (aus session.logic.php) gespeicherten Pfad oder zu start.php
                // Wegen header-injection sollten keine Pfade an den header via Get übergeben werden
            if(isset($_GET['redirect']) && isset($_SESSION['tc_redirect'])){
                $redirect = $_SESSION['tc_redirect'];
                unset($_SESSION['tc_redirect']);
            }else{
                $redirect = 'tc_start.php';
            }
            if (empty($team->details['trikot_farbe_1']))
                Form::info("Du kannst jetzt  Trikotfarben hinterlegen - diese werden in deinen Spielplänen angezeigt. "
                    . Form::link("tc_teamdaten_aendern.php", 'Hier kannst du sie eintragen.', icon:"launch"),
                    esc:false);
            if (empty($team->details['teamfoot']))
                Form::info("Dein Team hat noch kein Teamfoto hinterlegt."
                    . Form::link("../teamcenter/tc_teamdaten_aendern.php",
                        ' Hier kannst du ein Foto hochladen.', icon:"launch"),
                        esc:false);
            header('Location: ' . $redirect);
            die();
        }else{
            //Logdatei erstellen/beschreiben
            Form::log($log_file, "Falsches Passwort | Teamname: " . $teamname);
            Form::error("Falsches Passwort. Schreibe " . Form::mailto(Env::LAMAIL)
                . " um euer Passwort zurückzusetzen", esc:false);
        }
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Config::$page_width = "480px";
Config::$titel = "Teamcenter | Deutsche Einradhockeyliga";
Config::$content =
    "Im Teamcenter können Teams ihren Kader verwalten, ihre Teamdaten ändern, Emails versenden und sich zu Turnieren an-
     und abmelden.";
include '../../templates/header.tmp.php'; ?>

<form method="post" class="w3-card-4 w3-panel ">
    <h1 class="w3-text-primary">Teamcenter</h1>
    <p class="w3-text-grey">Im Teamcenter können Teams ihren Kader verwalten, ihre Teamdaten ändern und sich zu Turnieren an- und abmelden.</p>
        <div onclick='document.getElementById("teamname").value = "";document.getElementById("passwort").value = "";'
             class="no w3-right w3-text-red w3-hover-text-secondary" style="cursor: pointer;">
            <i class="material-icons">clear</i>
        </div>
        <label for="teamname"><i class="material-icons">group</i> Team:</label>
        <input class="w3-input w3-border-primary"
               value="<?=$_POST['teamname'] ?? ''?>"
               placeholder="Team"
               type="text"
               list="teams"
               id="teamname"
               name="teamname"
               required
        >
            <?=Form::datalist_teams()?>
    <p>
        <label for="passwort"><i class="material-icons">lock</i> Passwort:</label>
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
            <?= Form::icon("login") ?> Login
        </button>
    </p>
</form>

<?php include '../../templates/footer.tmp.php';