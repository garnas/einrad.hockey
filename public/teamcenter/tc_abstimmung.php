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

$abschluss = strtotime(ConfigService::ENDE);
$beginn = strtotime(ConfigService::BEGINN);
$team = TeamRepository::get()->team($_SESSION['logins']['team']['id']);
$abstimmung = AbstimmungRepository::get();
$crypt = VotingService::teamid_to_hash($team);

if (!empty($_SESSION['flash_info'])) {
    Html::info($_SESSION['flash_info']);
    unset($_SESSION['flash_info']);
}

if (isset($_POST["abgestimmt"])) {
    $stimme = $_POST;
    unset($stimme['abgestimmt']);
    $valid = ValidatorService::validate($stimme);   
    if ($valid['valid']) {
    
        $message = $abstimmung->setStimme($team, $crypt, $stimme);
        $_SESSION['flash_info'] = $message;
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit;
    
    } else {
        Html::error($valid['message']);
    }
}

$hasVote = $abstimmung->hasVote($team);
$data = $hasVote ? $abstimmung->getStimme($crypt) : [];

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Ligaausschuss-Wahlen 2026</h1>

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
        <?php foreach (ConfigService::NAMES as $id => $name): ?>
            <p>
                <input id="<?= $id ?>" type="checkbox" name="<?= $id ?>" <?= in_array($id, $data) ? 'checked' : '' ?> class="w3-check" >
                <label class="w3-hover-text-primary" style="cursor: pointer;" for="<?= $id ?>"><?= $name ?></label>
            </p>
        <?php endforeach; ?>
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

<?php
include '../../templates/footer.tmp.php';