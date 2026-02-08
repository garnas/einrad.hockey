<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;
use App\Service\Abstimmung\ConfigService;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth

$teams = TeamRepository::get()->activeLigaTeams();

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

    <div class="w3-margin-top">
        <p class="w3-text-secondary"><strong>Fragen</strong></p>
        <p><?= Html::link(Nav::LINK_FORUM, "Discord", "true", "chat") ?> oder <?= Html::mailto(Env::LAMAIL) ?></p>
    </div>    
    
    <div class="w3-margin-top">
        <a class="w3-button w3-primary w3-hover-secondary w3-block" href="tc_abstimmung.php"><i class="material-icons">how_to_vote</i> Zur Abstimmung</a>
    </div>

<?php
include '../../templates/footer.tmp.php';