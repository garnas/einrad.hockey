<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

//Turnierklasse erstellen
$turnier_id = $_GET['turnier_id'];
$akt_turnier = new Turnier($turnier_id);

//Existiert das Turnier?
if (empty($akt_turnier->details)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location: lc_turnierliste.php');
    die();
}

//Vorhandenes Ergebnis anzeigen
$teamliste = $akt_turnier->get_liste_spielplan();
$turnier_ergebnis = $akt_turnier->get_ergebnis();

//Formularauswertung

//Ergebnis löschen
if (isset($_POST['ergebnis_loeschen'])){
    $akt_turnier->delete_ergebnis();
    $akt_turnier->set_phase('spielplan');
    $akt_turnier->schreibe_log("Ergebnisse gelöscht. Phase -> Spielplan","Ligaausschuss");
    Form::affirm("Ergebnis wurde gelöscht. Das Turnier wurde in die Spielplanphase versetzt.");
    header("Location: lc_spielplan_verwalten.php?turnier_id=" . $akt_turnier->details['turnier_id']);
    die();
}

//Ergebnis eintragen
if (isset($_POST['ergebnis_eintragen'])){
    if (!Tabelle::check_ergebnis_eintragbar($akt_turnier)){
        Form::error("Turnierergebnis wurde nicht eingetragen");
        header("Location: lc_spielplan_verwalten.php?turnier_id=" . $akt_turnier->details['turnier_id']);
        die();
    }
    foreach ($teamliste as $index => $team){
        if (empty($_POST['team' . $index]) or empty($_POST['ergebnis' . $index]) or empty($_POST['platz' . $index])){
            Form::error("Formular wurde unvollständig übermittelt");
            header("Location: lc_spielplan_verwalten.php?turnier_id=" . $akt_turnier->details['turnier_id']);
            die();
        }
    }
    $akt_turnier->delete_ergebnis();
    if ($akt_turnier->details['art'] == 'final'){
        foreach ($teamliste as $index => $team){
            $akt_turnier->set_ergebnis($_POST['team' . $index], '0',$_POST['platz' . $index]);
        } 
    }else{
        foreach ($teamliste as $index => $team){
            $akt_turnier->set_ergebnis($_POST['team' . $index], $_POST['ergebnis' . $index],$_POST['platz' . $index]);
        }
    }
    $akt_turnier->set_phase('ergebnis');
    $akt_turnier->schreibe_log("Ergebnisse manuell eingetragen. Phase -> Ergebnis","Ligaausschuss");
    Form::affirm("Ergebnisse wurden manuell eingetragen. Das Turnier wurde in die Ergebnisphase versetzt.");
    header("Location: lc_spielplan_verwalten.php?turnier_id=" . $akt_turnier->details['turnier_id']);
    die();
}

//Spielplan dynamisch erstellen
if(isset($_POST['auto_spielplan_erstellen'])){
    if ($akt_turnier->details['phase'] == 'melde' && 3 < count($teamliste) && count($teamliste) < 8){
        $akt_turnier->set_phase('spielplan');
        $akt_turnier->schreibe_log('Phase -> Spielplan, Automatischer Spielplan', 'Ligaausschuss');
        Form::affirm("Das Turnier wurde in die Spielplan-Phase versetzt. Der dynamische Spielplan wird jetzt angezeigt.");
        header('Location: ../liga/spielplan.php?turnier_id=' . $akt_turnier->details['turnier_id']);
        die();
    }else{
        Form::error('Spielplan konnte nicht erstellt werden. Turnier wurde nicht in die Spielplanphase versetzt.');
    }
    
}

//Spielplan löschen
if(isset($_POST['auto_spielplan_loeschen'])){
    Spielplan::delete_spielplan($turnier_id);
    $akt_turnier->set_phase('melde');
    $akt_turnier->schreibe_log("Spielplan wurde aus der Datenbank gelöscht.\r\nPhase -> melde","Ligaausschuss");
    Form::affirm("Der dynamisch erstellte Spielplan wurde gelöscht. Das Turnier wurde in die Meldephase versetzt!");
    header('Location:' . db::escape($_SERVER['REQUEST_URI']));
    die();
}

//Spielplan oder Ergebnis manuell hochladen
if (isset($_POST['spielplan_hochladen'])){
    if (!empty($_FILES["spielplan_file"]["tmp_name"])){
        $target_dir = Config::BASE_LINK . "uploads/s/spielplan/";
        //PDF wird hochgeladen, target_file_pdf = false, falls fehlgeschlagen.
        $target_file_pdf = Neuigkeit::upload_pdf($_FILES["spielplan_file"], $target_dir);
        if($target_file_pdf === false){
            Form::error("Fehler beim Upload");
        }else{
            $akt_turnier->set_spielplan($target_file_pdf);
            if ($_POST['sp_or_erg'] == 'spielplan'){ //Das Turnier sollte erst in die Ergebnisphase versetzt werden, wenn auch wirklich ergebnisse vorhanden sind
                if (in_array($akt_turnier->details['phase'], ['melde','offen'])){
                    $akt_turnier->set_phase('spielplan');
                    Form::attention("Das Turnier wurde in die Spielplan-Phase versetzt.");
                }
                $akt_turnier->schreibe_log("Phase -> spielplan", "Ligaausschuss");
                $akt_turnier->schreibe_log("Spielplandatei manuell hochgeladen", "Ligaausschuss");
                Form::affirm("Spielplan wurde hochgeladen");
                Spielplan::delete_spielplan($turnier_id);
            }else{
                $akt_turnier->schreibe_log("Ergebnisdatei manuell hochgeladen", "Ligaausschuss");
                Form::affirm("Ergebnis wurde hochgeladen. Die Phase des Turniers wurde nicht verändert.");
                Spielplan::delete_spielplan($turnier_id);
            }
        }
    }else{
        Form::error("Es wurde kein Spielplan gefunden");
    }
}

//Spielplan löschen
if (isset($_POST['spielplan_delete'])){
    unlink($akt_turnier->details['link_spielplan']);
    $akt_turnier->set_spielplan('');
    $akt_turnier->schreibe_log("Spielplan- / Ergebnisdatei manuell gelöscht", "Ligaausschuss");
    Form::affirm("Spielplan wurde gelöscht.");
    Form::attention("Achtung die Phase muss manuell angepasst werden!");
}

//Hinweis Finalturniere-Ergebnis
if ($akt_turnier->details['art'] == 'final'){
    Form::attention("Beim Eintragen von Finalturnieren kann eine beliebige Punktzahl eingeben werden.");
}
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<!-- Überschrift -->
<h2 class="w3-text-primary">
    <span class="w3-text-grey">Spielplan/Ergebnis</span>
    <br>
    <?=$akt_turnier->details['tname']?> <?=$akt_turnier->details['ort']?>, <?=$akt_turnier->details['datum']?>
</h2>

<!-- Teamliste -->
<div class="w3-responsive">  
    <table class="w3-table w3-striped">
        <thead class="w3-primary">
            <tr>
                <th>Team ID</th>
                <th>Teamname</th>
                <th>Teamblock</th>
                <th>Wertigkeit</th>
            </tr>
        </thead>
        <?php foreach ($teamliste as $index => $team){?>
            <tr>
                <td><?=$team['team_id']?></td>
                <td><?=$team['teamname']?></td>
                <td><?=$team['tblock'] ?: 'NL'?></td>
                <td><?=$team['wertigkeit'] ?: 'Siehe Modus'?></td>
            </tr> 
        <?php } //end foreach?>
    </table>
</div>
<p><?=Form::link('lc_turnier_bearbeiten.php?turnier_id=' . $akt_turnier->details['turnier_id'],'<i class="material-icons">create</i> Turnierdaten bearbeiten')?></p>

<!-- Spielplan/Ergebnis-Erstellung -->
<h2 class="w3-text-primary w3-bottombar">Spielplan erstellen/löschen</h2>
<h3 class="w3-text-grey">Automatisch</h3>
<form method="post">
    <p>
        <input type="submit" name="auto_spielplan_loeschen" value="Automatischer Spielplan löschen" class="w3-button w3-secondary"> 
    </p>
    <p>
        <input type="submit" name="auto_spielplan_erstellen" value="Automatischer Spielplan erstellen" class="w3-button w3-tertiary"> 
    </p>
</form>
<h3 class="w3-text-grey">Manuell</h3>
<p class="w3-text-grey">Nur .pdf oder .xlsx Format</p>
<form method="post" enctype="multipart/form-data">
    <?php if(empty($akt_turnier->details['link_spielplan'])){?>
        <p>
            <input required type="file" name="spielplan_file" id="spielplan_file" class="w3-button w3-tertiary"> 
        </p>
        <p>
            <label class="w3-text-grey" for="sp_or_erg">Spielplan oder Ergebnis?</label><br>
            <select required id="sp_or_erg" name="sp_or_erg" class="w3-select w3-border w3-border-primary" style="max-width: 200px">
                <option selected disabled></option>
                <option value="spielplan">Spielplan</option>
                <option value="ergebnis">Ergebnis</option>
            </select>
        </p>
        <p>
            <input type="submit" name="spielplan_hochladen" value="Upload" class="w3-button w3-secondary"> 
        </p>
    <?php }else{ ?>
        <p><?=Form::link($akt_turnier->details['link_spielplan'], 'Spielplan/Ergebnis herunterladen');?></p>
        <p>
            <input type="submit" name="spielplan_delete" value="Spielplandatei löschen" class="w3-button w3-secondary"> 
        </p>
    <?php } //end if?>
</form>

<!-- Ergebnisse eintragen -->
<h2 class="w3-bottombar w3-text-primary">Ergebnisse manuell eintragen</h2>
<form method="post">
    <table class="w3-table w3-striped">
        <thead class="w3-primary">
            <tr>
                <th>#</th>
                <th>Teamname</th>
                <th>Turnierergebnis</th>
            </tr>
        </thead>
        <?php foreach ($teamliste as $index_a => $team){?>
            <tr>
                <td><?=$index_a?></td>
                <td>
                    <input type="hidden" name="platz<?=$index_a?>" value="<?=$index_a?>">
                    <select required class="w3-select w3-border w3-border-primary" name="team<?=$index_a?>">
                        <option disabled <?php if($akt_turnier->details['phase'] != "ergebnis"){?>selected<?php } //endif?>>Bitte wählen</option>
                        <?php foreach ($teamliste as $index_b => $team){?>
                        <option <?php if(($turnier_ergebnis[$index_a]['team_id'] ?? '') == $team['team_id']){?>selected<?php } //endif?> value="<?=$team['team_id']?>"><?=$team['teamname']?></option>
                        <?php } //end foreach?>
                    </select>
                </td>
                <td style="width: 30px"><input type="number" required class="w3-input w3-border-primary w3-border" value="<?=$turnier_ergebnis[$index_a]['ergebnis'] ?? ''?>" name="ergebnis<?=$index_a?>"></td>
            </tr> 
        <?php } //end foreach?>
    </table>
    <p>
        <input type="submit" name="ergebnis_eintragen" value="Ergebnis eintragen" class="w3-button w3-tertiary"> 
    </p>
    <p>
        <input type="submit" name="ergebnis_loeschen" value="Ergebnis löschen" class="w3-button w3-secondary"> 
    </p>
</form>

<?php include '../../templates/footer.tmp.php';