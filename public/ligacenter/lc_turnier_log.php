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

$turnierEntity = TurnierRepository::get()->turnier($turnier_id);

if ($turnierEntity === null){
    Html::notice("Turnier wurde nicht gefunden.");
}

;


/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php'; ?>

    <h2>Turnierlog</h2>
    <h2><?= TurnierSnippets::nameBrTitel($turnierEntity) ?></h2>
    <?= Html::link(TurnierLinks::details($turnierEntity), "Turnierdetails", icon:'info') ?>
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th>Zeit</th>
                <th>Akteur</th>
                <th>Aktion</th>
            </tr>
            <?php foreach ($turnierEntity->getLogs() as $log){?>
                <tr>
                    <td style="white-space: pre;"><?= $log->getZeit()->format("d.m.y h:i:s") ?></td>
                    <td><?= $log->getAutor() ?></td>
                    <td style="white-space: pre;"><?=$log->getLogText()?></td>
                </tr>
            <?php } //end forach?>
        </table>
    </div>
<?php include '../../templates/footer.tmp.php';