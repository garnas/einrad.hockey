<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth

$turniere = TurnierRepository::getAlleTurniere();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php'; ?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script>
        //Turnierergebnisse filtern
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#myDIV tbody").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>

    <h1 class="w3-text-grey">Turniere verwalten</h1>
    <p>
        <?= Html::link('lc_logs.php', 'Gesamtlog anzeigen', icon: 'info_outline') ?>
    </p>
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <?= Html::icon("search") ?><input id="myInput" class='w3-padding w3-border-0' style="width: 225px;"
                                          type="text" placeholder="Team suchen">
    </div>
    <div id="myDIV" class="w3-responsive w3-card-4">
        <table class="w3-table w3-bordered w3-striped" style="white-space: nowrap">
            <tr class='w3-primary'>
                <th>Turnier</th>
                <th><i>Phase<i></th>

            </tr>
            <?php foreach ($turniere as $turnier): ?>
            <tbody>
                <tr class="<?= TurnierSnippets::rowColor($turnier) ?>">
                    <td><?= TurnierSnippets::nameBrTitel($turnier) ?></td>
                    <td><?= TurnierSnippets::phase($turnier) ?></td>
                </tr>
                <tr>
                    <td colspan="4" style="white-space:normal;">
                        <?= $turnier->isCanceled() ? '<i>Abgesagt</i><br>' : '' ?>
                        <?= implode("<br>", TurnierLinks::getLinksFuerLa($turnier)) ?>
                    </td>
                </tr>
            </tbody>
            <?php endforeach; ?>
        </table>
    </div>
<?php
include '../../templates/footer.tmp.php';
