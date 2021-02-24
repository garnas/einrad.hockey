<!-- Navigationsleiste für das Ligacenter -->
<a class="no" href="<?= Config::BASE_URL ?>/teamcenter/tc_start.php">
    <nav class="w3-primary w3-container w3-hover-grey w3-card-4 w3-padding-small">
        <?= Form::icon("apps") ?>
        <span>
            Teamcenter - <?= $_SESSION['logins']['team']['name']. " (" . $_SESSION['logins']['team']['block'] . ")" ?>
        </span>
    </nav>
</a>