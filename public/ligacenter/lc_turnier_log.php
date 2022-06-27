<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

// Turnierobjekt erstellen
$turnier_id = (int) @$_GET['turnier_id'];
// Logs des Turnieres bekommen
$logs = nTurnier::get_log($turnier_id);

$turnierEntity = TurnierRepository::get()->turnier($turnier_id);
// Gelöschtes Turnier
$turnier = nTurnier::get($turnier_id);
if ($turnier === null & !empty($logs)){
    Html::notice("Turnier wurde gelöscht.");
}

// Turnier nicht gefunden
if ($turnier === null & empty($logs)){
    Html::notice("Es wurden keine Turnierlogs gefunden");
    header('Location: lc_turnierliste.php');
    die();
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php'; ?>

    <h2>Turnierlog</h2>
    <h2><?= TurnierSnippets::nameBrTitel($turnierEntity) ?></h2>
    <?= Html::link(TurnierLinks::details($turnierEntity)) ?>
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