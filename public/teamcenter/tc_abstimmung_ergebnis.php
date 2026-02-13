<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Service\Abstimmung\ResultService;
use App\Service\Abstimmung\ConfigService;
use App\Repository\Team\TeamRepository;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

$beginn = strtotime(ConfigService::BEGINN);
$abschluss = strtotime(ConfigService::ENDE);

$results = ResultService::getResult();
$participation = ResultService::getParticipation();
$teams = count(TeamRepository::get()->activeLigaTeams());

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Ligaausschusswahlen 2026</h1>

<?php if (!ConfigService::isFinished()): ?>

    <div class="w3-margin-top">
        <p class="w3-text-secondary"><strong>Ende der Abstimmung</strong></p>
        <?php Html::countdown($abschluss) ?>
    </div>

<?php else: ?>

    <h3 class="w3-text-secondary">Stimmen pro Person</h3>
    <em>Die Namen sind in alphabetischer Reihenfolge nach Nach- und Vornamen angeordnet.</em>

    <div class="w3-section">
        <?php foreach (ConfigService::NAMES as $key => $values): ?>
            <div class="w3-row w3-light-grey w3-margin-top w3-padding">
                <!-- large & medium -->
                <div class="w3-col l8 m8 w3-hide-small"><strong><?= $values['name'] ?></strong> (<?= $values['team'] ?>)</div>
                <div class="w3-col l2 m2 w3-hide-small w3-right-align" ><?= $results[$key] ?? 0 ?> / <?= $participation ?></div>
                <div class="w3-col l2 m2 w3-hide-small w3-right-align" ><?= ResultService::getPercentString($results[$key] ?? 0, $participation) ?></div>
                <!-- small -->
                <div class="w3-col w3-hide-large w3-hide-medium s8"><strong><?= $values['name'] ?></strong><br><?= $values['team'] ?></div>
                <div class="w3-col w3-hide-large w3-hide-medium s2 w3-right-align"><?= $results[$key] ?? 0 ?> / <?= $participation ?></div>
                <div class="w3-col w3-hide-large w3-hide-medium s2 w3-right-align" ><?= ResultService::getPercentString($results[$key] ?? 0, $participation) ?></div>
            </div>
        <?php endforeach; ?>
    </div>

    <h3 class="w3-text-secondary">Teilnehmende Teams</h3>
    <div class="w3-section">
        <div class="w3-row w3-light-grey w3-margin-top w3-padding">
            <div class="w3-col l8 m8 s8"><strong>Beteiligung</strong></div>
            <div class="w3-col l2 m2 s2 w3-right-align" ><?= $participation ?? 0 ?> / <?= $teams ?></div>
            <div class="w3-col l2 m2 s2 w3-right-align" ><?= ResultService::getPercentString($participation, $teams) ?></div>
        </div>
    </div>

<?php endif; ?>

<?php
include '../../templates/footer.tmp.php';