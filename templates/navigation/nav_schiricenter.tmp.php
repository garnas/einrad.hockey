<!-- Navigationsleiste für das Schiricenter -->
<a class="no" href="<?= Env::BASE_URL ?>/schiricenter/sc_start.php">
    <nav class="w3-secondary <?php if (!isset($_SESSION['logins']['team'])) {?>w3-card-4<?php }//end if?> w3-hover-grey w3-container w3-padding-small">
        <?= Html::icon("apps") ?> Schiricenter
    </nav>
</a>
