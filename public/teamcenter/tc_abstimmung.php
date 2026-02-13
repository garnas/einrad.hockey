<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Service\Abstimmung\ValidatorService;
use App\Service\Abstimmung\VotingService;
use App\Repository\Abstimmung\AbstimmungRepository;
use App\Repository\Team\TeamRepository;
use App\Service\Abstimmung\ConfigService;


require_once '../../init.php';
Helper::ensure_no_request_logging();

require_once '../../logic/session_team.logic.php'; //Auth
$teams = TeamRepository::get()->activeLigaTeams();
$beginn = strtotime(ConfigService::BEGINN);
$abschluss = strtotime(ConfigService::ENDE);

$team = TeamRepository::get()->team($_SESSION['logins']['team']['id']);
$abstimmung = AbstimmungRepository::get();
$crypt = VotingService::teamIdToHash($team);


if (empty($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

if (!empty($_SESSION['flash_info'])) {
    Html::info($_SESSION['flash_info']);
    unset($_SESSION['flash_info']);
}

if (isset($_POST["abgestimmt"])) {
    if (
            !isset($_POST['csrf_token']) ||
            !hash_equals($_SESSION['csrf_token'], $_POST['csrf_token'])
    ) {
        trigger_error("Ungültiger CSRF-Token", E_USER_ERROR);
    }
    unset ($_SESSION['csrf_token']);

    $stimme = $_POST;
    unset($stimme['abgestimmt']);
    unset($stimme['csrf_token']);

    $valid = ValidatorService::validate($stimme);
    if ($valid['valid']) {
        $message = $abstimmung->setStimme($team, $crypt, $stimme);
        Html::info($message);
        Helper::reload();
    } else {
        Html::error($valid['message']);
    }
}

$hasVote = $abstimmung->hasVote($team);
$data = $hasVote ? $abstimmung->getStimme($crypt) : [];

$beginn = strtotime(ConfigService::BEGINN);
$abschluss = strtotime(ConfigService::ENDE);

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

    <h1 class="w3-text-primary">Ligaausschusswahlen 2026</h1>
    <div class="w3-margin-top">
        <p class="w3-text-secondary"><strong>Informationen</strong></p>
        <p>Jedes Team kann bis zu drei Kandidaten jeweils eine Stimme geben. Dein Team kann auch abstimmen, ohne eine
            Stimme
            an einen der Kandidaten zu vergeben (Enthaltung).</p>
    </div>

    <div class="w3-margin-top">
        <p class="w3-text-secondary"><strong>Hinweise zur Abstimmung</strong></p>
        <p>Die Abstimmung ist anonym - die Stimmzuordnung zu den Teams wird verschlüsselt. Aus technischen Gründen ist
            eine nachträgliche Zuordnung von Stimmen jedoch theoretisch möglich.</p>
        <p>Eine Einsicht und Änderung eurer abgegebenen Stimmen ist im Nachhinein möglich.
        <p>
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

    <div class="w3-panel w3-light-grey">
        <h3 class="w3-text-primary">Status</h3>
        <?php if ($hasVote): ?>
            <p class="w3-text-green">Es wurde eine Stimme für dein Team hinterlegt.</p>
        <?php else: ?>
            <p class="w3-text-red">Es ist keine Stimme für dein Team hinterlegt.</p>
        <?php endif; ?>
    </div>

    <form id="stimme" method="post">
        <div class="w3-panel w3-light-grey">

            <h2 class="w3-text-primary"><?= (!$hasVote) ? 'Jetzt abstimmen' : 'Stimme ändern' ?></h2>

            <p>Das Team <?= $team->getName() ?> stimmt für die folgende(n) Person(en):</p>
            <?php if (!$data): ?>
                <p>Dein Team hat abgestimmt, aber keine Stimme für einen der Kandidaten vergeben.</p>
            <?php endif; ?>
            <?php foreach (ConfigService::NAMES as $id => $values): ?>
                <p>
                    <input id="<?= $id ?>" type="checkbox"
                           name="<?= $id ?>" <?= in_array($id, $data) ? 'checked' : '' ?> class="w3-check">
                    <label class="w3-hover-text-primary" style="cursor: pointer;"
                           for="<?= $id ?>"><strong><?= $values['name'] ?></strong> (<?= $values['team'] ?>)</label>
                </p>
            <?php endforeach; ?>
            <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">
            <p>
                <em>Die Namen sind in alphabetischer Reihenfolge nach Nach- und Vornamen angeordnet.</em>
            </p>

        </div>

        <div class="w3-panel w3-light-grey">

            <p>
                <button type="submit" name="abgestimmt" class="w3-block w3-button w3-primary w3-hover-secondary">
                    <i class="material-icons">how_to_vote</i> <?= (!$hasVote) ? 'Stimme abgeben' : 'Stimme ändern' ?>
                </button>
            </p>

        </div>

    </form>

<?php endif; ?>

<?php
include '../../templates/footer.tmp.php';