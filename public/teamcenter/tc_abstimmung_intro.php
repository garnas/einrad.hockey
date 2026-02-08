<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;
use App\Service\Abstimmung\ConfigService;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

$teams = TeamRepository::get()->activeLigaTeams();
$beginn = strtotime(ConfigService::BEGINN);
$abschluss = strtotime(ConfigService::ENDE);


/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Ligaausschuss-Wahlen 2026</h1>

<div class="w3-margin-top">
    <p class="w3-text-secondary"><strong>Informationen</strong></p>
    <p>...</p>
</div>

<div class="w3-margin-top">
    <p class="w3-text-secondary"><strong>Hinweise zur Abstimmung</strong></p>
    <p>Die Abstimmung ist anonym - die Stimmzuordnung wird verschlüsselt. Eine Einsicht und Änderung eurer abgegebenen Stimme ist im Nachhinein möglich.</p>
</div>

<?php if (ConfigService::isPreparing()): ?>
    <div class="w3-margin-top">
        <p class="w3-text-secondary"><strong>Beginn der Abstimmung</strong></p>
        <?php Html::countdown($beginn) ?>
    </div>
<?php elseif ($beginn <= time() && time() < $abschluss): ?>
    <div class="w3-margin-top">
        <p class="w3-text-secondary"><strong>Ende der Abstimmung</strong></p>
        <?php Html::countdown($abschluss) ?>
    </div>
<?php endif; ?>

<div class="w3-margin-top">
    <p class="w3-text-secondary"><strong>Fragen</strong></p>
    <p><?= Html::link(Nav::LINK_FORUM, "Discord", "true", "chat") ?> oder <?= Html::mailto(Env::LAMAIL) ?></p>
</div>

<?php if (ConfigService::isRunning()): ?>
    <div class="w3-margin-top">
        <a class="w3-button w3-primary w3-hover-secondary w3-block" href="tc_abstimmung.php"><i class="material-icons">how_to_vote</i> Zur Abstimmung</a>
    </div>
<?php elseif (ConfigService::isFinished()): ?>
    <div class="w3-margin-top">
        <a class="w3-button w3-primary w3-hover-secondary w3-block" href="tc_abstimmung_ergebnis.php"><i class="material-icons">how_to_vote</i> Zum Ergebnis</a>
    </div>
<?php endif; ?>

<?php
include '../../templates/footer.tmp.php';