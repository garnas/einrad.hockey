<!-- Navigationsleiste für das Ligacenter -->
<a class="no" href="<?= Env::BASE_URL ?>/teamcenter/tc_start.php">
    <nav class="w3-primary w3-container w3-hover-grey w3-card-4 w3-padding-small">
        <?= Html::icon("apps") ?>
        <span>
            Teamcenter - <?= e($_SESSION['logins']['team']['name']). " (" . e($_SESSION['logins']['team']['block']) . ")" ?>
        </span>
    </nav>
</a>