<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Event\Turnier\nLigaBot;
use App\Repository\Team\TeamRepository;
use App\Service\Team\TeamService;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

$deaktivierte_teams = Team::get_deactive();

//Formularauswertung

// Als Ligateam anmelden
if (isset($_POST['anmelden'])){

    $team = TeamRepository::get()->findByName($_POST['teamname']);
    
    if ($team && $team->isLigaTeam()){
        TeamService::removeTeamSession();
        TeamService::createTeamSession($team);
        Helper::log("login.log", "Erfolgreich       | via Ligacenter: " . $_SESSION['logins']['la']['login']
            . " als " . $_SESSION['logins']['team']['name']);
        Html::info("Login via Ligaausschuss erfolgreich");
        Helper::reload('/teamcenter/tc_start.php');
    }
    Html::error("Anmeldung als Team nicht möglich, da der Teamname keinem Ligateam zugeordnet werden konnte.");
}

// Ligateam deativieren
if (isset($_POST['deaktivieren'])){
    $teamname = $_POST['teamname'] ?? '';
    $team_id = Team::name_to_id($teamname);

    if (Team::is_ligateam($team_id)){
        Team::deactivate($team_id);
        Html::info("Das Team $teamname wurde deaktiviert.");
        header('Location: ../ligacenter/lc_admin.php');
        die();
    }
    Html::error("Teamname wurde nicht gefunden. Team wurde nicht deaktiviert.");
}

// Ligateam reaktivieren
if (isset($_POST['reaktivieren'])){
    $team_id = $_POST['team_id'] ?? '';
    $teamname = Team::id_to_name($team_id);
    
    if (!empty($teamname)){
        Team::activate($team_id);
        Html::info("Das Team $teamname wurde reaktiviert.");
        header('Location: ../ligacenter/lc_admin.php');
        die();
    }
    Html::error("Teamname wurde nicht gefunden. Team wurde nicht deaktiviert.");
}

if (isset($_POST['gitpull'])){

    if (Env::IS_LOCALHOST ?? true) {

        Html::error("Update im Localhost nicht sinnvoll, da eventuelle Änderungen gelöscht werden würden.");

    } else {

        $hash = LigaLeitung::get_details($_SESSION['logins']['la']['login'])['passwort'];
        $password = $_POST['password'];

        if (password_verify($password, $hash)) {
            $output = shell_exec("sh " . Env::BASE_PATH . "/system/shell/gitpull.sh");

            Helper::log("git.log", $output ?? 'EMPTY OUTPUT');
            db::debug($output);
            $output = shell_exec("sh " . Env::BASE_PATH . "/system/shell/doctrine.sh");
            Helper::log("doctrine.log", $output ?? 'EMPTY OUTPUT');
            db::debug($output);

            Helper::reload();

        } else {

            Html::error("Falsches Passwort.");

        }

    }
}

if (isset($_POST['gitlogs'])){

    $output = shell_exec("sh " . Env::BASE_PATH . "/system/shell/gitlog.sh");
    db::debug($output);

}

if (isset($_POST['gitstatus'])){

    $output = shell_exec("sh " . Env::BASE_PATH . "/system/shell/gitstatus.sh");
    db::debug($output);

}

//Ligabot ausführen
if (isset($_POST['ligabot'])){
    nLigaBot::ligaBot();
}

//Datenbank sichern
if (isset($_POST['sichern'])){
    db::sql_backup();
}

//Datenbank sichern
if (isset($_POST['mailbot'])){
    MailBot::mail_bot();
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';?>

<h4 class="w3-bottombar w3-text-primary">Als Ligateam anmelden</h4>
<form method='post'>
    <label class="w3-text-primary" for="teamname">Team wählen</label>
    <input type="text"
           class="w3-input w3-border w3-border-primary"
           placeholder="Team eingeben"
           list="teams"
           id="teamname"
           name="teamname">
        <?= Html::datalist_teams() ?>
    <p>
        <input type='submit' name='anmelden' value='Als Ligateam anmelden' class="w3-button w3-secondary">
    </p>
</form>

<h4 class="w3-bottombar w3-text-primary">Team deaktivieren</h4>
<form method='post' onsubmit="return confirm('Soll das ausgewählte Team wirklich deaktiviert werden?')">
    <label class="w3-text-primary" for="teamname">Team wählen</label>
    <input type="text"
           class="w3-input w3-border w3-border-primary"
           placeholder="Team eingeben"
           list="teams"
           id="teamname"
           name="teamname">
        <?= Html::datalist_teams() ?>
    <p>
        <input type='submit' name='deaktivieren' value='Team deaktivieren' class="w3-button w3-secondary">
    </p>
</form>


<h4 class="w3-bottombar w3-text-primary">Team reaktivieren</h4>
<form method='post'>
    <label class="w3-text-primary" for="team_id">Team wählen</label>
    <select required class="w3-select w3-border w3-border-primary" list="teams" id="team_id" name="team_id">
        <option disabled selected>Bitte deaktives Team wählen</option>
            <?php foreach ($deaktivierte_teams as $team){?>
                <option value=<?=$team['team_id']?>><?=$team['teamname']?></option>
            <?php } //end foreach?>
    </select>
    <p>
        <input type='submit' name='reaktivieren' value='Team reaktiveren' class="w3-button w3-secondary">
    </p>
</form>
<!-- Mailbot -->
<form method='post'>
    <h4 class="w3-bottombar w3-text-primary">Automatische Mails versenden</h4>
    <p>
        <input type='submit' name='mailbot' value='Mailbot starten' class="w3-button w3-secondary">
    </p>
</form>
<!-- DB sichern -->
<form method='post'>
    <h4 class="w3-bottombar w3-text-primary">Datenbank sichern</h4>
    <p>
        <input type='submit' name='sichern' value='Datenbank sichern' class="w3-button w3-secondary">
    </p>
</form>
<!-- Ligabot -->
<form method='post'>
    <h4 class="w3-bottombar w3-text-primary">Ligabot ausführen</h4>
    <p>
        <input type='submit' name='ligabot' value='Ligabot ausführen' class="w3-button w3-secondary">
    </p>
</form>

<form method='post'>
    <h4 class="w3-bottombar w3-text-primary">Git Logs</h4>
    <p>
        <input type='submit' name='gitlogs' value='Git Logs' class="w3-button w3-secondary">
    </p>
</form>
<form method='post'>
    <h4 class="w3-bottombar w3-text-primary">Git Status</h4>
    <p>
        <input type='submit' name='gitstatus' value='Git Status' class="w3-button w3-secondary">
    </p>
</form>
<form method='post'>
    <h4 class="w3-bottombar w3-text-primary">Git Update Website</h4>
    <p>
        <label>LA-Passwort eingeben:</label>
        <input type='password' name='password' class="w3-input w3-light-grey">
    </p>
    <p>
        <input type='submit' name='gitpull' value='Update Website von Master' class="w3-button w3-green w3-block">
    </p>
</form>

<?php include '../../templates/footer.tmp.php';