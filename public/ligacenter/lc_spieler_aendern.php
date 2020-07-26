<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

$spieler_liste = Spieler::get_spielerliste(); //Liste aller Spielernamen und IDs [0] => vorname nachname [1] => spieler_id
$idliste = array();
//db::debug($spieler_liste);
if (in_array(123,$spieler_liste)){

}
foreach ($spieler_liste as $spieler_id => $name){
    array_push($idliste, $spieler_id);
}

//Formularauswertung Spielerauswahl
if (isset($_POST['spieler_auswahl'])){
    $pos = count(explode(" ",$_POST['spieler_auswahl']))-1; //Postion der SpielerID finden
    $spieler_id = explode(" ",$_POST['spieler_auswahl'])[$pos]; //SpielerID extrahieren
    header('Location: ' . db::escape($_SERVER['PHP_SELF']) . '?spieler_id=' . $spieler_id);
    die();
}

//Formular unten nur Anzeigen wenn eine existierende SpielerID übergeben wurde wurde
$spieler_id='';
if(isset($_GET['spieler_id'])){
    if (array_key_exists($_GET['spieler_id'], $spieler_liste)){
        $akt_spieler = new Spieler($_GET['spieler_id']);
        $daten = $akt_spieler->get_spieler_details();
        $daten['teamname'] = Team::teamid_to_teamname($daten['team_id']);
        $show_form = true;
    }else{
        Form::error("Spieler wurde nicht gefunden");
    }
}

//Formularauswertung Spielerdaten verändern
if (isset($_POST['spieler_aendern'])){
    $vorname = $_POST['vorname'];
    $nachname = $_POST['nachname'];
    $jahrgang = $_POST['jahrgang'];
    $geschlecht = $_POST['geschlecht'];
    $teamname = $_POST['teamname'];
    $letzter_saison = $_POST['letzte_saison'];
    $error = false;
    if (empty($vorname) or empty($nachname) or empty($jahrgang) or empty($geschlecht) or empty($teamname)) {
        Form::error("Bitte Formular ausfüllen");
        $error = true;
    }
    $team_id = Team::teamname_to_teamid($teamname);
    if (empty($team_id)) {
        Form::error("Das Team $teamname wurde nicht gefunden");
        $error = true;
    }
    if (1900 > $jahrgang  or $jahrgang > date('Y', Config::time_offset())) {
        Form::error("Ungültiger Jahrgang (Jahreszahl vierstellig ausschreiben)");
        $error = true;
        db::debug($jahrgang);
    }

    //Alle Datenbankeinträge außer team_id
    $changed=false;
    if (!$error){
        $liste=array("vorname","nachname","jahrgang","geschlecht","schiri","junior","letzte_saison");
        foreach ($liste as $entry) {
            if($daten[$entry] != $_POST[$entry]){
                $akt_spieler->set_spieler_detail($entry, $_POST[$entry]);
                $changed = true;
            }
        }

        if ($daten['team_id'] != $team_id){
            $akt_spieler->set_spieler_detail('team_id', $team_id);
            $changed = true;
        }
        if ($changed) {
            Form::affirm("Spielerdaten wurden geändert");
            header('Location: lc_kader.php?team_id=' . $team_id);
            die();
        }else{
            Form::error("Es wurden keine Daten geändert");
        }
    }
}

//Formularauswertung Spieler löschen
if (isset($_POST['delete_spieler'])){
    $akt_spieler->delete_spieler();
    Form::affirm ("Der Spieler " . $daten['vorname'] . " " . $daten['nachname'] . " mit der ID " . $daten['spieler_id'] . " wurde gelöscht.");
    header("Location: " . $_SERVER['PHP_SELF']);
    die();
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<!-- Spielerauswahlfeld -->
<div class="w3-panel w3-card-4">
    <form method="post">
        <p>
            <label for="spieler"><h3 class="w3-text-primary">Spieler wählen</h3 ></label>
            <input onchange="this.form.submit();" type="text" placeholder="Spieler eingeben" style="max-width:400px" class="w3-input w3-border w3-border-primary" list="spielerliste" id="spieler" name="spieler_auswahl">
                <datalist id="spielerliste">
                    <?php
                    foreach ($spieler_liste as $spieler_id => $name){ ?>
                        <option value='<?=$name . ' ' . $spieler_id?>'>
                    <?php } //end foreach ?>
                </datalist>
        </p>
        <p>
            <input type="submit" class="w3-button w3-tertiary" value="Spieler wählen">
        </p>
    </form>
</div>
<?php if ($show_form ?? false){?>
    <form class="w3-card-4 w3-panel" method='post'>
        <!-- Spieler-Details -->
        <h3>Spieler mit der ID <?=$daten['spieler_id']?> ändern</h3>
        <p>
            <label class="w3-text-primary" for="vorname">Vorname</labeL>
            <input class="w3-input w3-border w3-border-primary" type="text" name="vorname" value="<?=$daten['vorname']?>" autocomplete="off" required>
        </p>
        <p>
            <label class="w3-text-primary" for="nachname">Nachname</labeL>
            <input class="w3-input w3-border w3-border-primary" type="text" name="nachname" value="<?=$daten['nachname']?>" autocomplete="off" required>
        </p>
        <p>
            <label class="w3-text-primary" for="jahrgang">Jahrgang</labeL>
            <input class="w3-input w3-border w3-border-primary" type="number" name="jahrgang" value="<?=$daten['jahrgang']?>" autocomplete="off" required>
        </p>
        <p>
            <label class="w3-text-primary" for="geschlecht">Geschlecht</labeL>
            <select style="height:40px" class='w3-input w3-border w3-border-primary' name='geschlecht'>
                <option <?php if($daten['geschlecht'] == 'm'){?>selected <?php }?> value='m'>m</option>
                <option <?php if($daten['geschlecht'] == 'w'){?>selected <?php }?> value='w'>w</option>
                <option <?php if($daten['geschlecht'] == 'd'){?>selected <?php }?>value='d'>d</option>
            </select>
        </p>
        <p>
            <label class="w3-text-primary" for="schiri">Schiri</labeL>
            <select style="height:40px" class='w3-input w3-border w3-border-primary' name='schiri'>
                <option <?php if($daten['schiri'] == Config::SAISON + 2){?>selected<?php }?> value='<?=Config::SAISON + 2?>'><?=Form::get_saison_string(Config::SAISON + 2)?></option>
                <option <?php if($daten['schiri'] == Config::SAISON + 1){?>selected<?php } ?> value='<?=Config::SAISON + 1?>'><?=Form::get_saison_string(Config::SAISON + 1)?></option>
                <option <?php if($daten['schiri'] == Config::SAISON){?>selected<?php }?> value='<?=Config::SAISON?>'><?=Form::get_saison_string()?></option>
                <option <?php if(empty($daten['schiri'])){?>selected<?php }?> value=''>kein Schiri</option>
                <option <?php if(($daten['schiri']) == 'Ausbilder/in'){?>selected<?php }?> value='Ausbilder/in'>Ausbilder/in</option>
            </select>
        </p>
        <p>
            <input class="w3-check" type="checkbox" id="junior" name="junior" <?php if ($daten['junior'] == "Ja"){?> checked <?php } //endif?> value="Ja">
            <label for="junior"><span class="w3-text-primary w3-hover-text-secondary" style="cursor: pointer;"><i> Junior-Schiedsrichter</i></span></label>
        </p>
        <p>
            <label class="w3-text-primary"for="teamname">Team ändern</label>
            <input type="text" class="w3-input w3-border w3-border-primary" value="<?=$daten['teamname']?>" list="teams" id="teamname" name="teamname">
                <?=Form::datalist_teams()?>
                <?=Form::link("lc_kader.php?team_id=" . $daten['team_id'], '<i class="material-icons">group</i> Zum Teamkader der ' . $daten['teamname'])?>
        </p>
        <p>
            <label class="w3-text-primary" for="letzte_saison">Letzte aktive Saison</label>
            <input class="w3-input w3-border w3-border-primary" type="number" id="letzte_saison" name="letzte_saison" value="<?=$daten['letzte_saison']?>" autocomplete="off" required>
            <span class="w3-text-grey">(Saison Nr. <?=$daten['letzte_saison']?> entspricht Saison <?=Form::get_saison_string($daten['letzte_saison'])?>)</span>
        </p>
        <p>
            <input class="w3-button w3-tertiary w3-block" type="submit" name="spieler_aendern" value="Spieler ändern">
        </p>
    </form>
    <form onsubmit="return confirm('Der Spieler mit der ID <?=$daten['spieler_id']?> (<?=$daten['vorname'] . ' ' . $daten['nachname']?>) wird gelöscht werden.');" class="w3-container w3-card-4 w3-panel" method="POST">
        <p>
            <input class="w3-button w3-secondary w3-block" type="submit" name="delete_spieler" value="Spieler löschen">
        </p>
    </form>
<?php } //Ende IF

include '../../templates/footer.tmp.php';