<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_team.logic.php'; //auth

$panels = Nav::get_tc_start();

Html::info("In euren " . Html::link(Env::BASE_URL . "/teamcenter/tc_teamdaten.php", "Teamdaten")
    . " könnt ihr mögliche Bonus-Freilose für frühzeitig gesetzte Freilose und angemeldete Turniere sehen.", esc: false);
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php'; ?>

<h1 class='w3-center w3-text-primary'>Hallo <?= e($_SESSION['logins']['team']['name']) ?>!</h1>
<div class="flex-container">
    <?php foreach ($panels as $panel): ?>
        <a class="no flex-item <?= $panel[2] ?> w3-round-xlarge w3-hover-opacity" href="<?= $panel[0] ?>">
            <?= $panel[1] ?>
        </a>
    <?php endforeach; ?>
</div>

<?php include '../../templates/footer.tmp.php'; ?>