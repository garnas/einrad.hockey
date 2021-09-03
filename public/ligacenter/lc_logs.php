<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

// Logs des Turnieres bekommen
$logs = Logs::get_logs();

db::debug($logs);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

Html::$titel = "Gesamtlogs | Ligacenter";
Html::$content = "Hier wird der gesamte Log aller Turniere angezeigt.";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr class="w3-primary">
            <th>Zeit</th>
            <th>Turnier ID</th>
            <th>Aktion</th>
            <th>Akteur</th>
        </tr>
        <?php foreach ($logs as $log){?>   
            <tr>
                <td style="white-space: pre;"><?=$log['zeit']?></td>
                <td><?=$log['turnier_id']?></td>
                <td style="white-space: pre;"><?=$log['log_text']?></td>
                <td><?=$log['autor']?></td>
            </tr>
        <?php } //end forach?>
    </table>
</div>

<?php
// (14) Einfügen des Footers
include Env::BASE_PATH . '/templates/footer.tmp.php';