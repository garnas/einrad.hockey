<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

// Logs des Turnieres bekommen
$logs = Logs::get_turnier_logs();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

Html::$titel = "Gesamtlogs Turniere | Ligacenter";
Html::$content = "Hier wird der gesamte Log aller Turniere angezeigt.";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

<br>
<?= Html::link('lc_turnierliste.php', '<span class="material-icons">sports_hockey</span> Zurück zur Turnierliste') ?>
<h2 class="w3-text-grey">Gesamtübersicht über alle Vorgänge</h2>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
<script>
    $(document).ready(function() {
        $("#myInput").on("keyup", function() {
            var value = $(this).val().toLowerCase();
            $("#myDIV .row").filter(function() {
                $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
            });
        });
    });
</script>

<div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
    <?= Html::icon("search") ?><input id="myInput" class='w3-padding w3-border-0' style="width: 225px;" type="text" placeholder="Durchsuchen">
</div>

<div id="myDIV">
    <div class="w3-responsive w3-card">
        <table class="w3-table w3-striped">
            <tr class="w3-primary">
                <th>Zeit</th>
                <th>Turnier ID</th>
                <th>Aktion</th>
                <th>Akteur</th>
            </tr>
            <?php foreach ($logs as $log) { ?>
                <tr class="row">
                    <td style="white-space: pre;"><?= $log['zeit'] ?></td>
                    <td><?= $log['turnier_id'] ?></td>
                    <td style="white-space: pre;"><?= $log['log_text'] ?></td>
                    <td><?= $log['autor'] ?></td>
                </tr>
            <?php } //end forach
            ?>
        </table>
    </div>
</div>

<?php
// (14) Einfügen des Footers
include Env::BASE_PATH . '/templates/footer.tmp.php';
