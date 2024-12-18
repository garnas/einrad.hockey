<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //auth

$centerpanels = Nav::get_lc_start();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$page_width = "660px";
include '../../templates/header.tmp.php'; ?>


<h1 class='w3-center w3-text-primary'>Hallo <?= LigaLeitung::get_details($_SESSION['logins']['la']['login'])['vorname'] ?>!</h1>
<?= Html::link(Env::BASE_URL . "/ligacenter/lc_turnierstats.php", "turniere.xlsx", extern: true) ?>
<br>
<?= Html::link(Env::BASE_URL . "/ligacenter/lc_spielerstats.php","spieler.xlsx", extern: true) ?>
<br>
<?= Html::link(Env::BASE_URL . "/ligacenter/lc_teamstats.php", "teams.xlsx", extern: true) ?>
<br>
<?= Html::link(Env::BASE_URL . "/schiricenter/schiritest_stats.php", "schiritest.xlsx", extern: true) ?>
<br>

<div id="messen" class="">
    <!-- Misst die Fensterbreite des Browsers, um die Centerpanels gleichmäßig verteilen zu können -->
    <div id="apps" class="w3-content">
        <div class="w3-row">

            <?php foreach ($centerpanels as $centerpanel) { ?>
                <a class="" href="<?= $centerpanel[0] ?>">
                    <div class="w3-col <?= $centerpanel[2] ?> w3-round-xxlarge w3-hover-opacity w3-display-container centerpanels">
                        <div class="w3-display-middle w3-large w3-center">
                            <?= $centerpanel[1] ?>
                        </div>
                    </div>
                </a>
            <?php } //end foreach?>

        </div> <!-- w3-row -->
    </div> <!-- w3-apps -->
</div> <!-- w3-messen -->

<script>
    //setzt die Margin der centerpanels-Quadrate so, dass sie immer gleich verteilt sind
    window.addEventListener("resize", centerpanels_anordnung);
    centerpanels_anordnung();
</script>

<?php include '../../templates/footer.tmp.php'; ?>

