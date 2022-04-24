<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

// Turnierobjekt erstellen
$turnier = nTurnier::get((int) @$_GET['turnier_id']);

// Logs des Turnieres bekommen
$logs = $turnier->get_log();

// Gelöschtes Turnier
if (empty($turnier->get_turnier_id()) & !empty($logs)){
    Html::notice("Turnier wurde gelöscht.");
}

// Turnier nicht gefunden
if (empty($turnier->get_turnier_id()) & empty($logs)){
    Html::notice("Es wurden keine Turnierlogs gefunden");
    header('Location: lc_turnierliste.php');
    die();
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>
<?php if (!empty($turnier->get_turnier_id())){?>
    <h2>Turnierlog <?=$turnier->get_datum() . ' ' . $turnier->get_tname()?> <?=$turnier->get_ort()?> (<?=$turnier->get_tblock()?>)</h2>
<?php }else{ ?>
    <h2>Turnierlog Turnier-ID <?=$turnier->get_turnier_id()?></h2>
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