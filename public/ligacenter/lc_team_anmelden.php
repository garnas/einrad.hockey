<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/la_session.logic.php'; //Auth

//Turnierobjekt erstellen
$turnier_id = $_GET['turnier_id'];
$akt_turnier = new Turnier ($turnier_id);

//Turnierdaten bekommen
$daten = $akt_turnier->daten;

//Existiert das Turnier?
if (empty($daten)){
    Form::error("Turnier wurde nicht gefunden");
    header('Location: ../liga/turniere.php');
    die();
}

//Turnieranmeldungen bekommen
$anmeldungen = $akt_turnier->get_anmeldungen();

//Formularauswertung

/////////////Team als Ligaausschuss abmelden/////////////
if (isset($_POST['abmelden'])){
    foreach ($anmeldungen as $liste) {
        foreach ($liste as $team) {
            if (isset($_POST['abmelden' . $team['team_id']])){
                $akt_turnier->abmelden($team['team_id']);
                $akt_turnier->schreibe_log("Abmeldung: " . $team['teamname'] . "\r\nvon Liste: " . $team['liste'], "Ligaausschuss");
                if ($team['liste'] == 'warte'){
                    $akt_turnier->warteliste_aktualisieren();
                }
                Form::affirm ($team['teamname'] . " wurde abgemeldet");
                header('Location: ../ligacenter/lc_team_anmelden.php?turnier_id=' . $daten['turnier_id']);
                die();
            }
        }
    }
    Form::error("Es wurde kein Team abgemeldet. Es ist ein Fehler aufgetreten.");  
}

/////////////Ligateam als Ligaausschuss anmelden/////////////
if (isset($_POST['team_anmelden'])){
    $liste = $_POST['liste'];
    $teamname = $_POST['teamname'];
    $team_id = Team::teamname_to_teamid($teamname);
    $error = false;

    //Postion auf der Warteliste
    if ($liste == 'warte'){
        $pos = $_POST['pos'];
    }else{
        $pos = 0;
    }

    //Existiert der ausgewählte Teamname?
    if (empty($team_id)){
        $error = true;
        Form::error("Team wurde nicht gefunden");
    }

    //Ist das Team bereits angemeldet?
    if ($akt_turnier->check_team_angemeldet($team_id)){
        $error = true;
        Form::error("Team ist bereits angemeldet");
    }

    if (!$error){
        $akt_turnier->team_anmelden($team_id, $liste, $pos);
        $akt_turnier->schreibe_log("Anmeldung: $teamname\r\nTeamblock: " . (Tabelle::get_team_block($team_id) ?: 'NL') . " Turnierblock: " . $daten['tblock'] ."\r\nListe: $liste (<i>WartePos: $pos</i>)", "Ligaausschuss");
        Form::affirm ("$teamname wurde angemeldet");
        header('Location: ../ligacenter/lc_team_anmelden.php?turnier_id=' . $daten['turnier_id']);
        die();
    }
}

/////////////Nichtligateam anmelden/////////////
if (isset($_POST['nl_anmelden'])){
    $liste = $_POST['nl_liste'];
    $teamname = $_POST['nl_teamname'];

    if ($liste == 'warte'){
        $pos = $_POST['nl_pos'];
    }else{
        $pos = 0;
    }

    //Check ob schon ein Nichtligateam mit diesem Namen in der Datenbank existiert
    //Nichtligateams bekommen immer einen Stern hinter ihrem Namen
    $team_id = Team::teamname_to_teamid($teamname . '*');
    if (!$akt_turnier->check_team_angemeldet($team_id)){
        $akt_turnier->nl_anmelden($teamname, $liste, $pos);
        $akt_turnier->schreibe_log("Anmeldung: $teamname*\r\nTeamblock: " . (Tabelle::get_team_block($team_id) ?: 'NL') . "\r\nListe:  $liste (<i>WartePos: $pos </i>)", "Ligaausschuss");
        Form::affirm("$teamname wurde angemeldet auf Liste: $liste");
        header('Location: ../ligacenter/lc_team_anmelden.php?turnier_id=' . $daten['turnier_id']);
        die();
    }else{
        Form::error("Ein Nichtligateam mit diesem Namen ist bereits angemeldet");
    }
}

/////////////Warteliste neu Durchnummerieren/////////////
if (isset($_POST['warteliste_aktualisieren'])){

    $akt_turnier->warteliste_aktualisieren("Ligaausschuss");
    //Log wird automatisch in der Funktion geschrieben, Argument: Autor

    Form::affirm("Warteliste wurde aktualisiert");
    header('Location: ../ligacenter/lc_team_anmelden.php?turnier_id=' . $daten['turnier_id']);
    die();
}

/////////////Spielenliste von der Warteliste neu auffuellen/////////////
if (isset($_POST['spieleliste_auffuellen'])){
    $error = false;

    //Hat das Turnier noch freie Plätze?
    if ($akt_turnier->anzahl_freie_plaetze() <= 0){
        $error = true;
        Form::error("Spielen-Liste ist bereits voll");
    }

    //Ist das Turnier in der Meldephase?
    if ($daten['phase'] != 'melde'){
        $error = true;
        Form::error("Turnier befindet sich nicht in der Meldephase");
    }
    
    if (!$error){
        $akt_turnier->spieleliste_auffuellen("Ligaausschuss");
        Form::affirm("Spielenliste wurde aufgefüllt");
        header('Location: ../ligacenter/lc_team_anmelden.php?turnier_id=' . $daten['turnier_id']);
        die();
    }else{
        Form::error('Spielen-Liste wurde nicht aufgefüllt');
    }
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h2 class="w3-text-primary">Teamanmeldung</h2>
<h3 class="w3-text-grey"><?=$daten['tname'] ?: 'Turnier'?> in <?=$daten['ort']?> am <?=$daten['datum']?> (<?=$daten['tblock']?>)</h3>

<!-- Links -->
<p><a class="no w3-text-blue w3-hover-text-secondary" href='../liga/turnier_details.php?turnier_id=<?=$daten['turnier_id']?>'>Turnierdetails</a>
<p><a class="no w3-text-blue w3-hover-text-secondary" href='../ligacenter/lc_turnier_bearbeiten.php?turnier_id=<?=$daten['turnier_id']?>'>Turnier bearbeiten (Ligaausschuss)</a></p>
<p><a class="no w3-text-blue w3-hover-text-secondary" href='../ligacenter/lc_turnier_log.php?turnier_id=<?=$daten['turnier_id']?>'>Turnierlog einsehen (Ligaausschuss)</a></p>

<!-- Anzeigen der angemeldeten Teams und gleichzeitig Abmeldeformular -->
<div class="w3-panel w3-card-4">
    <form method='post'>
        <h3>Angemeldete Teams</h3>
                <h4>Spielen-Liste:</h4>
                <p>
                <?php if (!empty($anmeldungen['spiele'])){?>

                <?php foreach ($anmeldungen['spiele'] as $team){?>
                <?=$team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?: 'NL'?>)</span>
                <input type='submit' class='w3-button w3-text-blue' name='abmelden<?=$team['team_id']?>' value='Abmelden'><br>
                <?php }//end foreach?>

                <?php }else{?><i>leer</i><?php } //endif?> 

                <h4>Meldeliste:</h4>
                <p>
                <?php if (!empty($anmeldungen['melde'])){?>

                <?php foreach ($anmeldungen['melde'] as $team){?>
                <?=$team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?: 'NL'?>)</span>
                <input type='submit' class='w3-button w3-text-blue' name='abmelden<?=$team['team_id']?>' value='Abmelden'><br>
                <?php }//end foreach?>

                <?php }else{?><i>leer</i><?php } //endif?>
                </p>

                <h4>Warteliste:</h4>
                <p>
                <?php if (!empty($anmeldungen['warte'])){?>

                <?php foreach ($anmeldungen['warte'] as $team){?>
                <?=$team['position_warteliste'] . ". " . $team['teamname']?> <span class="w3-text-primary">(<?=$team['tblock'] ?? 'NL'?>)</span>
                <input type='submit' class='w3-button w3-text-blue' name='abmelden<?=$team['team_id']?>' value='Abmelden'><br>
                <?php }//end foreach?>

                <?php }else{ ?><i>leer</i><?php } //endif?> 
                </p>

        <p>Freie Plätze: <?=$daten['plaetze'] - count(($anmeldungen['spiele'] ?? array()))?> von <?=$daten['plaetze']?></p>
        <p class="w3-small w3-text-primary">Phase: <?=$daten['phase']?></p>
        <!-- hidden input, um zu erkennen ob ein Team abgemeldet werden soll -->
        <input type='hidden' name='abmelden' value='abmelden'>
    </form>

    <!-- Spielenliste auffuellen und Warteliste aktualisieren -->
    <form method='post'>
        <p>
        <input type='submit' class='w3-button w3-block w3-tertiary' name='warteliste_aktualisieren' value='Warteliste aktualisieren'>
        </p>
        <p>
        <input type='submit' class='w3-button w3-block w3-tertiary' name='spieleliste_auffuellen' value='Warteliste -> Spielen-Liste'>
        </p>
    </form>
    
</div>

<!-- An- und Abmeldung -->
<div class="w3-card-4 w3-panel">
    <form class="" method="post">
        <h3>Team anmelden</h3> 
        <p>
        <label for="teamname" class='w3-text-primary'>Team wählen:</label><br>
        <input required type="text" style="max-width:400px" placeholder="Team eingeben" class="w3-input w3-border w3-border-primary" list="teams" id="teamname" name="teamname">
        <?=Form::datalist_teams()?>
        </p>
        <p>
        <label for="liste" class='w3-text-primary'>Liste wählen:</label>
        <select required class='w3-select w3-border w3-border-primary' name='liste' id='liste'>
            <option selected disabled value=''>--</option>
            <option value='spiele'>Spielenliste</option>
            <option value='melde'>Meldeliste</option>
            <option value='warte'>Warteliste</option>
        </select>
        </p>
        <p>
        <label for="pos" class='w3-text-primary'>Position auf der Warteliste</label>
        <select required class='w3-select w3-border w3-border-primary' name='pos' id='pos'>
        <option selected value='<?=count($anmeldungen['warte'] ?? array())+1?>'>Ende der Warteliste</option>
            <?php $i=1; for ($i; $i<=count($anmeldungen['warte'] ?? array()); $i++){?>
                <option value='<?=$i?>'>Position <?=$i?></option>
            <?php } //end for?>
        </select>
        </p>
        <p>
        <input type='submit' class='w3-button w3-margin-bottom w3-block w3-tertiary w3-right' name='team_anmelden' value='Anmelden'>
        </p>
    </form>
</div>

<div class="w3-panel w3-card-4">
    <form class="" method="post">
        <h3>Nichtligateam anmelden</h3> 
        <p>
        <label for="nl_teamname" class='w3-text-primary'>Teamname</label><br>
        <input required type="text" style="max-width:400px" class="w3-input w3-border w3-border-primary" id="nl_teamname" name="nl_teamname">
        </p>
        <p>
        <label for="nl_liste" class='w3-text-primary'>Liste wählen:</label>
        <select required class='w3-select w3-border w3-border-primary' name='nl_liste' id='nl_liste'>
            <option selected disabled value=''>--</option>
            <option value='spiele'>Spielen-Liste</option>
            <option value='warte'>Warteliste</option>
        </select>
        </p>
        <p>
        <label for="nl_pos" class='w3-text-primary'>Position auf der Warteliste</label>
        <select required class='w3-select w3-border w3-border-primary' name='nl_pos' id='nl_pos'>
            <option selected value='<?=count($anmeldungen['warte'] ?? array()) + 1?>'>Ende der Warteliste</option>
            <?php $i=1; for ($i; $i<=count($anmeldungen['warte'] ?? array()); $i++){?>
                <option value='<?=$i?>'>Position <?=$i?></option>
            <?php } //end for?>
        </select>
        </p>
        <p>
        <input type='submit' class='w3-button w3-margin-bottom w3-block w3-tertiary w3-right' name='nl_anmelden' value='Anmelden'>
        </p>
    </form>
</div>

<?php include '../../templates/footer.tmp.php';