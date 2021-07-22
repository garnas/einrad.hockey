<!-- Navigationsleiste für das Ligacenter -->
<a class="no" href="<?= Env::BASE_URL ?>/ligacenter/lc_start.php">
    <nav class="w3-tertiary <?php if (!isset($_SESSION['logins']['team'])){?>w3-card-4<?php }//end if?> w3-hover-grey w3-container w3-padding-small">
        <?= Html::icon("apps") ?> Ligacenter
    </nav>
</a>