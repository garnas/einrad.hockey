<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////

use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //Auth


$team_id = $_SESSION['logins']['team']['id'];
$turniere = TurnierRepository::getSetzlisteTurniere($team_id);
$saison = Config::SAISON;

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
?>

<h1 class="w3-text-primary">Turnierreport-Liste</h1>
<p class="w3-border-top w3-border-grey w3-text-grey">Saison <?=Html::get_saison_string($saison)?></p>

<div>
    <div class="w3-row w3-primary w3-border-bottom w3-border-grey">
        <div class="w3-quarter w3-padding-8">Datum</div>
        <div class="w3-quarter w3-padding-8">Ort</div>
        <div class="w3-quarter w3-padding-8">Block</div>
        <div class="w3-quarter w3-padding-8">Details</div>
    </div>
</div>

<?php foreach ($turniere as $key => $turnier): ?>
    <div class="w3-row w3-border-bottom w3-border-grey <?= $key % 2 == 0 ? '' : 'w3-light-grey' ?>">
        <div class="w3-quarter w3-padding-8"><?=TurnierSnippets::datum($turnier)?></div>
        <div class="w3-quarter w3-padding-8"><?=$turnier->getDetails()->getOrt()?></div>
        <div class="w3-quarter w3-padding-8"><?=$turnier->getBlock()?></div>
        <div class="w3-quarter w3-padding-8"><?=Html::link("../teamcenter/tc_turnier_report.php?turnier_id=" . $turnier->id(), 'Turnierreport', icon:'')?></div>
    </div>
<?php endforeach; ?>

<?php
include '../../templates/footer.tmp.php';