<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

//Turnierobjekt erstellen
$turnier_id = $_GET['turnier_id'] = 913;
$akt_turnier = new Turnier ($turnier_id);
$tbericht = new TurnierBericht ($turnier_id);
//db::debug($akt_turnier->get_liste_spielplan());

//Existiert das Turnier?
if (empty($akt_turnier->daten)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location: ../teamcenter/tc_turnierliste_anmelden.php');
    die();
}
if ($akt_turnier->get_team_liste($_SESSION['team_id']) != 'spiele'){
    Form::error("Fehlende Berechtigung");
    header('Location: ../teamcenter/tc_turnierliste_anmelden.php');
    die();
}

//Liste der Teams
$teams = $akt_turnier->get_liste_spielplan();
//Kader und Ausbilder
$kader_array = $akt_turnier->get_kader_kontrolle();
$ausbilder_liste = array();
$spieler_liste = array();
foreach ($kader_array as $team_id => $kader){
    foreach ($kader as $spieler_id => $spieler){
        if($spieler['schiri'] == 'Ausbilder/in'){
            $ausbilder_liste[$spieler_id] = $spieler;
        }
        $spieler_liste[$spieler_id] = $spieler;
    }
}

//Spielerausleihe
$spieler_ausleihen = $tbericht->get_spieler_ausleihen();
    //Spielerausleihe löschen
foreach ($spieler_ausleihen as $ausleihe_id => $ausleihe){
    if (isset($_POST['del_ausleihe_' . $ausleihe_id])){
        $tbericht->delete_spieler_ausleihe($ausleihe_id);
        Form::affirm("Spielerausleihe wurde entfernt.");
        header('Location:' . db::escape($_SERVER['PHP_SELF']));
        die();
    }
}
    //Spielerausleihe hinzufügen
if (isset($_POST['new_ausleihe'])){
    $name = $_POST['ausleihe_name'];
    $team_ab = $_POST['ausleihe_team_ab'];
    $team_auf = $_POST['ausleihe_team_auf'];
    $tbericht->new_spieler_ausleihe($name,$team_ab,$team_auf);
    Form::affirm("Spielerausleihe wurde hinzugefügt.");
    header('Location:' . db::escape($_SERVER['PHP_SELF']));
    die();
}

//Zeitstrafen
$zeitstrafen = $tbericht->get_zeitstrafen();
    //Zeitstrafe löschen
foreach ($zeitstrafen as $zeitstrafe_id => $zeitstrafe){
    if (isset($_POST['del_zeitstrafe_' . $zeitstrafe_id])){
        $tbericht->delete_zeitstrafe($zeitstrafe_id);
        Form::affirm("Zeitstrafe wurde entfernt.");
        header('Location:' . db::escape($_SERVER['PHP_SELF']));
        die();
    }
}
    //Zeitstrafe hinzufügen
if (isset($_POST['new_zeitstrafe'])){
    $name = $_POST['zeitstrafe_spieler'];
    $team_a = $_POST['zeitstrafe_team_a'];
    $team_b = $_POST['zeitstrafe_team_b'];
    $bericht = $_POST['zeitstrafe_bericht'];
    $tbericht->new_zeitstrafe($name,$team_a,$team_b,$bericht);
    Form::affirm("Spielerausleihe wurde hinzugefügt.");
    header('Location:' . db::escape($_SERVER['PHP_SELF']));
    die();
}

//Turnierbericht
if (isset($_POST['set_turnierbericht'])){
    $bericht = $_POST['turnierbericht'];
    $kader_check = $_POST['kader_check'];
    if ($kader_check == "kader_checked"){
        $kader_check = 'Ja';
    }else{
        $kader_check = 'Nein';
    }
    $tbericht->set_turnier_bericht($bericht, $kader_check);
    Form::affirm("Turnierbericht wurde gespeichert");
    header('Location:' . db::escape($_SERVER['PHP_SELF']));
    die();
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';?>

<!-- Ausbilder -->
<?php if (!empty($ausbilder_liste)){?>
    <h1 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -26%" class="material-icons">school</i> Ausbilder</h1>
    <ul class='w3-ul w3-margin-left w3-leftbar w3-border-tertiary'>
        <?php foreach ($ausbilder_liste as $spieler){?>
            <li><?=$spieler['vorname'] . ' ' . $spieler['nachname']?> (<i><?=Team::teamid_to_teamname($team_id)?></i>)</li>
        <?php }//end foreach?>
    </ul>
<?php }//endif?>

<!-- Kader -->
<h1 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -26%" class="material-icons">groups</i> Kader und Schiedsrichter</h1>
<?php foreach ($kader_array as $team_id => $kader){?>
    <ul class='w3-ul w3-margin-left w3-leftbar w3-border-tertiary'>
        <li class="w3-hover-tertiary" style="cursor: pointer;" onclick="openTab('<?=$team_id?>')"><?=Team::teamid_to_teamname($team_id)?></li>
    </ul>
<?php }//end foreach?>
<?php foreach ($kader_array as $team_id => $kader){?>
    <div id="<?=$team_id?>" class="tab" style="display:none; max-width: 600px">
        <h3><?=Team::teamid_to_teamname($team_id)?></h3>
        <div class="w3-responsive w3-card">
            <table class="w3-table w3-striped">
                <tr class="w3-primary">
                    <th>Spieler-ID</th>
                    <th>Spieler</th>
                    <th>Schiri</th>
                </tr>
                <?php foreach ($kader as $spieler_id => $spieler){?>
                    <tr class="<?php if(!empty($spieler['schiri'])){?>w3-pale-green<?php } //endif?>">
                        <td><?=$spieler_id?></td>
                        <td><?=$spieler['vorname'] . ' ' .  mb_substr($spieler['nachname'],0,1, "utf-8") . '.'?></td>
                        <td><?php if (!empty($spieler['schiri'])){?><i class="material-icons">check_circle</i> <?=Form::get_saison_string($spieler['schiri'])?><?php } //endif?></td>
                    </tr>
                <?php }//end foreach?>
            </table>
        </div>
    </div>
<?php }//end foreach?>

<!-- Spielerausleihe -->
<h1 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -26%" class="material-icons">accessibility</i> Spielerausleihe</h1>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th>Name</th>
            <th>Aufnehmendes Team</th>
            <th>Abgebendes Team</th>
            <th>Löschen</th>
        </tr>
        <?php foreach ($spieler_ausleihen as $ausleihe){?>
            <tr>
                <td><?=$ausleihe['spieler']?></td>
                <td><?=$ausleihe['team_auf']?></td>
                <td><?=$ausleihe['team_ab']?></td>
                <td><form method="post"><input type="submit" class="w3-button w3-text-primary" value="X" name="del_ausleihe_<?=$ausleihe['ausleihe_id']?>"></form></td>
            </tr>
        <?php }//end foreach?>
    </table>
</div>

<!-- Spielerausleihe hinzufügen -->
<button onclick="document.getElementById('modal_ausleihe').style.display='block'" class="w3-section w3-button w3-tertiary">Spielerausleihe hinzufügen</button>
<div id="modal_ausleihe" class="w3-modal">
    <form method="post" class="w3-card-4 w3-panel w3-round w3-container w3-modal-content">
        <span onclick="document.getElementById('modal_ausleihe').style.display='none'" class="w3-button w3-large w3-text-secondary w3-display-topright">&times;</span>
        <h1 class="w3-text-primary">Spielerausleihe hinzufügen</h1>
        <p>
            <label for="ausleihe_name">Name des Spielers</label>
            <input required class="w3-input w3-border w3-border-primary" type="text" name="ausleihe_name" id="ausleihe_name"></input>   
        </p>
        <p>
            <label for="ausleihe_team_auf">Aufnehmendes Team</label>
            <select required name="ausleihe_team_auf" id="ausleihe_team_auf" class="w3-select w3-input w3-border w3-border-primary">
                <option selected disabled>--</option>
                <?php foreach($teams as $team){?>
                    <option><?=$team['teamname']?></option>
                <?php } //end foreach?>
            </select>
        </p>
        <p>
            <label for="ausleihe_team_ab">Abgebendes Team</label>
            <input class="w3-input w3-border w3-border-primary" placeholder="Team eingeben" type="text" list="teams" id="ausleihe_team_ab" name="ausleihe_team_ab" required>
                <?=Form::datalist_teams()?>
        </p>
        <p>
            <input type="submit" value="Hinzufügen" name="new_ausleihe" class="w3-button w3-tertiary">
        </p>
    </form>
</div>

<!-- Zeitstrafen -->
<h1 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -26%" class="material-icons">schedule</i> Zeitstrafen</h1>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th>Dauer</th>
            <th>Spieler</th>  
            <th>Spielpaarung</th>
            <th>Löschen</th>
        </tr>
        <?php foreach ($zeitstrafen as $zeitstrafe){?>
            <tr>
                <td><?=$zeitstrafe['dauer']?></td>
                <td><?=$zeitstrafe['spieler']?></td>
                <td><?=$zeitstrafe['team_a']?> - <?=$zeitstrafe['team_b']?></td>
                <td><form method="post"><input type="submit" class="w3-button w3-text-primary" value="X" name="del_zeitstrafe_<?=$zeitstrafe['zeitstrafe_id']?>"></form></td>
            </tr>
            <tr>
                <td colspan="5"><?=nl2br($zeitstrafe['beschreibung'])?></td>
            </tr>
        <?php }//end foreach?>
    </table>
</div>

<!-- Zeitstrafe hinzufügen -->
<button onclick="document.getElementById('modal_zeitstrafe').style.display='block'" class="w3-section w3-button w3-tertiary">Zeitstrafe hinzufügen</button>
<div id="modal_zeitstrafe" class="w3-modal">
    <form method="post" class="w3-card-4 w3-panel w3-round w3-container w3-modal-content">
        <span onclick="document.getElementById('modal_zeitstrafe').style.display='none'" class="w3-button w3-large w3-text-secondary w3-display-topright">&times;</span>
        <h1 class="w3-text-primary">Zeitstrafe hinzufügen</h1>
        <p>
            <label for="zeitstrafe_spieler">Spieler</label>
            <input type="text" placeholder="Name eingeben" class="w3-input w3-border w3-border-primary" list="spielerliste" id="zeitstrafe_spieler" name="zeitstrafe_spieler">
                <datalist id="spielerliste">
                    <?php
                    foreach ($spieler_liste as $spieler_id => $spieler){ ?>
                        <option value='<?=$spieler['vorname'] . ' ' .  mb_substr($spieler['nachname'],0,1, "utf-8") . '.' . ' | ' . Team::teamid_to_teamname($spieler['team_id'])?>'>
                    <?php } //end foreach ?>
                </datalist>
        </p>
        <p>
            <label for="dauer">Dauer</label>
            <select name="dauer" id="dauer" class="w3-select w3-input w3-border w3-border-primary">
                <option>2 min</option>
                <option>5 min</option>
                <option>Gesamtes Spiel</option>
            </select>
        </p>
        <p>
            <label for="zeitstrafe_team_a">Spielpaarung</label>
            <select id="zeitstrafe_team_a" name="zeitstrafe_team_a" class="w3-select w3-input w3-border w3-border-primary">
                <option disabled selected>--</option>
                <?php foreach($teams as $team){?>
                    <option><?=$team['teamname']?></option>
                <?php } //end foreach?>
            </select>
            <label for="zeitstrafe_team_b" class="w3-text-grey">versus</label>
            <select id="zeitstrafe_team_b" name="zeitstrafe_team_b" class="w3-select w3-input w3-border w3-border-primary">
                <option disabled selected>--</option>
                <?php foreach($teams as $team){?>
                    <option><?=$team['teamname']?></option>
                <?php } //end foreach?>
            </select>
        </p>
        <p>
            <label for="zeitstrafe_bericht">Grund</label>
            <textarea class="w3-input w3-border w3-border-primary" rows="3" id="zeitstrafe_bericht" name="zeitstrafe_bericht" required><?=stripcslashes($_POST['text'] ?? '')?></textarea>
            <span class="w3-text-grey">Eine detailierte Beschreibung der Situation sollte gegebenenfalls in den Turnierbericht aufgenommen werden.</span>  
        </p>
        <p>
            <input type="submit" name="new_zeitstrafe" value="Hinzufügen" class="w3-button w3-tertiary">
        </p>
    </form>
</div>

<!-- Turnierbericht -->
<h1 class="w3-text-primary"><i style="font-size: 36px; vertical-align: -26%" class="material-icons">info</i> Turnierbericht</h1>
<form method="post">
    <p>
        <input <?php if ($tbericht->kader_check()){?>selected<?php } //endif?>class="w3-check" value="kader_checked" type="checkbox" name="kader_check" id="kader_check"></input>
        <label for="kader_check" class="w3-hover-text-secondary w3-text-primary" style="cursor: pointer;"> Kader wurden kontrolliert</label>           
    </p>
    <p>
        <label for="spieler_id">Turnierbericht</label>
        <textarea class="w3-input w3-border w3-border-primary"  placeholder="Wie war das Turnier? Besondere Vorkomnisse, wie zum Beispiel die Verspätung eines Teams, können hier eingetragen werden." rows="12" id="turnierbericht" name="turnierbericht" required><?=stripcslashes($_POST['text'] ?? '')?><?=$tbericht->get_turnier_bericht();?></textarea>
    </p>
    <input type="submit" value="Speichern" name="set_turnierbericht" class="w3-button w3-tertiary">
</form>

<script>
// Get the modal
var modal1 = document.getElementById('modal_ausleihe');
var modal2 = document.getElementById('modal_zeitstrafe');
// When the user clicks anywhere outside of the modal, close it
window.onclick = function(event) {
  if (event.target == modal1) {
    modal1.style.display = "none";
  }
  if (event.target == modal2) {
    modal2.style.display = "none";
  }
}
</script>

<?php include '../../templates/footer.tmp.php';