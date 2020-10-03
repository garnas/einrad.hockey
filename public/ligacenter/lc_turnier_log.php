<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php'; //Auth

//Turnierobjekt erstellen
$turnier_id = $_GET['turnier_id'];
$akt_turnier = new Turnier ($turnier_id);

//Turnierdaten bekommen
$daten = $akt_turnier->daten;

//Logs des Turnieres bekommen
$logs = $akt_turnier->get_logs();

//Gelöschtes Turnier
if (empty($daten) & !empty($logs)){
    Form::attention("Turnier wurde gelöscht.");
}

//Gelöschtes Turnier
if (empty($daten) & empty($logs)){
    Form::attention("Es wurden keine Turnierlogs gefunden");
    header('Location: lc_turnierliste.php');
    die();
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>
<?php if (!empty($daten)){?>
    <h2>Turnierlog <?=$daten['datum'] . ' ' . $daten['tname']?> <?=$daten['ort']?> (<?=$daten['tblock']?>)</h2>
<?php } //endif?>
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th>Zeit</th>
            <th>Akteur</th>
            <th>Aktion</th>
        </tr>
        <?php foreach ($logs as $log){?>   
            <tr>
                <td style="white-space: pre;"><?=$log['zeit']?></td>
                <td><?=$log['autor']?></td>
                <td style="white-space: pre;"><?=$log['log_text']?></td>
            </tr>
        <?php } //end forach?>
    </table>
</div>
<?php include '../../templates/footer.tmp.php';