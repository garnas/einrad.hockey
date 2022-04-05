<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

// Logs des Turnieres bekommen
$ausleihen = Logs::get_spielerausleihe();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

Html::$titel = "Spielerausleihe | Ligacenter";
Html::$content = "Hier werden alle Spielerausleihen der gesamten Saison angezeigt.";
include Env::BASE_PATH . '/templates/header.tmp.php'; ?>

<br>
<?= Html::link('lc_turnierliste.php', '<span class="material-icons">sports_hockey</span> Zurück zur Turnierliste') ?>
<h2 class="w3-text-grey">Alle Spielerausleihen der Saison <?=Html::get_saison_string()?></h2>

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
                <th>Turnier</th>
                <th>Datum</th>
                <th>Ort</th>
                <th>Spieler</th>
                <th>Aufnehmendes Team</th>
                <th>Abgebendes Team</th>
            </tr>
            <?php foreach ($ausleihen as $spieler): ?>
                <tr class="row">
                    <td><?= Html::link('lc_turnier_report.php?turnier_id=' . $spieler['turnier_id'], $spieler['turnier_id']) ?></td>
                    <td><?= strftime("%d.%m.%Y", strtotime($spieler['datum'])) ?></td>
                    <td><?= $spieler['ort'] ?></td>
                    <td><?= $spieler['spieler'] ?></td>
                    <td><?= $spieler['team_auf'] ?></td>
                    <td><?= $spieler['team_ab'] ?></td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>

<?php
// (14) Einfügen des Footers
include Env::BASE_PATH . '/templates/footer.tmp.php';
