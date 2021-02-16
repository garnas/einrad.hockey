<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

$deaktivierte_teams = Team::get_deactive();

//Formularauswertung

//Als Ligateam anmelden
if (isset($_POST['anmelden'])){
    $teamname = dbi::escape($_POST['teamname']);
    $team_id = Team::name_to_id($teamname);
    
    if (Team::is_ligateam($team_id)){
        //unset($_SESSION['la_login_name']);
        //unset($_SESSION['logins']['la']);
        $_SESSION['team_id'] =  $team_id;
        $_SESSION['teamname'] = $teamname;
        $_SESSION['teamblock'] = Tabelle::get_team_block($team_id);
        Form::info("Login via Ligaausschuss erfolgreich");
        header('Location: ../teamcenter/tc_start.php');
        //Logdatei erstellen/beschreiben
        Form::log("login.log", "Erfolgreich       | via Ligacenter: " . $_SESSION['la_login_name'] . " als " . $_SESSION['teamname']);
        die();
    }else{
        Form::error("Anmeldung als Team nicht möglich, da der Teamname keinem Ligateam zugeordnet werden konnte.");
    }
}

//Ligateam deativieren
if (isset($_POST['deaktivieren'])){
    $teamname = $_POST['teamname'] ?? '';
    $team_id = Team::name_to_id($teamname);
    if (Team::is_ligateam($team_id)){
        Team::deactivate($team_id);
        Form::info("Das Team $teamname wurde deaktiviert.");
        header('Location: ../ligacenter/lc_admin.php');
        die();
    }else{
        Form::error("Teamname wurde nicht gefunden. Team wurde nicht deaktiviert.");
    } 
}

//Ligateam reaktivieren
if (isset($_POST['reaktivieren'])){
    $team_id = $_POST['team_id'] ?? '';
    $teamname = Team::id_to_name($team_id);
    
    if (!empty($teamname)){
        Team::activate($team_id);
        Form::info("Das Team $teamname wurde reaktiviert.");
        header('Location: ../ligacenter/lc_admin.php');
        die();
    }else{
        Form::error("Teamname wurde nicht gefunden. Team wurde nicht deaktiviert.");
    } 
}

//Ligabot ausführen
if (isset($_POST['ligabot'])){
    //LigaBot::zuruecksetzen(); //Setzt alle Turniere in die offene Phase zurück
    LigaBot::liga_bot();
}

//Datenbank sichern
if (isset($_POST['sichern'])){
    dbi::sql_backup();
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
    <input type="text" class="w3-input w3-border w3-border-primary" placeholder="Team eingeben" list="teams" id="teamname" name="teamname">
        <?=Form::datalist_teams();?>
    <p>
        <input type='submit' name='anmelden' value='Als Ligateam anmelden' class="w3-button w3-secondary">
    </p>
</form>

<h4 class="w3-bottombar w3-text-primary">Team deaktivieren</h4>
<form method='post' onsubmit="return confirm('Soll das ausgewählte Team wirklich deaktiviert werden?')">
    <label class="w3-text-primary" for="teamname">Team wählen</label>
    <input type="text" class="w3-input w3-border w3-border-primary" placeholder="Team eingeben" list="teams" id="teamname" name="teamname">
        <?=Form::datalist_teams();?>
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
    </p>
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
<?php include '../../templates/footer.tmp.php';