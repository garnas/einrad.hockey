<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Ligaleitung\Service\LigaleitungService;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //auth

$panels = Nav::get_lc_start();
$downloads = Nav::get_lc_downloads();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php'; ?>

<h1 class='w3-center w3-text-primary'>Hallo <?= e(LigaleitungService::getLoggedInLigaleitungByFunktion("ligaausschuss")?->getSpieler()->getVorname()) ?>!</h1>
<div class="flex-container">
    <?php foreach ($panels as $panel): ?>
        <a class="no flex-item <?= $panel[2] ?> w3-round-xlarge w3-hover-opacity" href="<?= $panel[0] ?>">
            <?= $panel[1] ?>
        </a>
    <?php endforeach; ?>
</div>

<div class="flex-container w3-border-top w3-border-grey">
    <?php foreach ($downloads as $download): ?>
        <a class="no flex-item <?= $download[2] ?> w3-round-xlarge w3-hover-opacity" href="<?= $download[0] ?>">
            <?= $download[1] ?>
        </a>
    <?php endforeach; ?>
</div>

<?php include '../../templates/footer.tmp.php'; ?>