<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Entity\Turnier\Turnier;
use App\Repository\Team\TeamRepository;
use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

// Relevante Turniere finden
$turniere = TeamRepository::getAusrichterTurniere($teamEntity);

if ($turniere->isEmpty()) {
    // Wenn keine eigenen Turniere eingetragen sind, wird auf die TC-Startseite umgeleitet
    Html::notice(
        'Dein Team richtet zurzeit kein Turnier aus - '
        . Html::link('tc_turnier_erstellen.php', 'Erstelle ein Turnier', icon: 'create')
        . 'um es verwalten zu können und Turniereinstellungen zu ändern.',
        esc: false
    );
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

    <h1 class="w3-text-primary w3-center">Eigene Turniere verwalten</h1>
    <div class="w3-responsive w3-card-4">
        <table class="w3-table w3-bordered w3-striped" style="white-space: nowrap">
            <tr class='w3-primary'>
                <th>Turnier</th>
                <th><i>Phase<i></th>

            </tr>
            <?php foreach ($turniere as $turnier): ?>
                <tr class="<?= TurnierSnippets::rowColor($turnier) ?>">
                    <td><?= TurnierSnippets::nameBrTitel($turnier) ?></td>
                    <td><?= TurnierSnippets::phase($turnier) ?></td>
                </tr>
                <tr>
                    <td colspan="4" style="white-space:normal;">
                        <?= $turnier->isCanceled() ? '<i>Abgesagt</i><br>' : '' ?>
                        <?= implode("<br>", TurnierLinks::getLinksFuerAusrichter($turnier)) ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
<?php include '../../templates/footer.tmp.php';