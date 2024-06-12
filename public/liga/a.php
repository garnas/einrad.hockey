<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

// Turnier-ID
$turnier_id = 1193;

// Spielplan laden
$turnier = nTurnier::get($turnier_id);
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Deutsche Meisterschaft Spielplan";
Html::$content = "Der Spielplan für das Einradhockey-Turnier in " . $turnier->get_ort()
    . " am " . $datum;

include '../../templates/header.tmp.php';
if ($turnier->get_phase() == "ergebnis") {
    Html::set_confetti();
}

?>
    <!-- Überschrift -->
    <h1 class="w3-text-grey">9er-Spielplan</h1>
    <h3><?= $turnier->get_tname() ?></h3>
    <h2 class="w3-text-primary">
        <?= $turnier->get_ort() ?><br>15.06 & 16.06
    </h2>
    <!-- Links -->
    <div class="pdf-hide">
        <?= Html::link("../liga/turnier_details.php?turnier_id=" . $turnier->get_turnier_id(), "Alle Turnierdetails", true, 'launch') ?>
        <?php if (isset($_SESSION['logins']['team'])) { ?>
            <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->get_turnier_id(), 'Zum Turnierreport', true, 'launch') ?>
        <?php } else { ?>
            <?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->get_turnier_id(), 'Zum Turnierreport', true, 'launch') ?>
        <?php } // endif?>
        <?php if (($_SESSION['logins']['team']['id'] ?? 0) == $turnier->get_ausrichter() && !(Helper::$teamcenter ?? false) && $turnier->get_phase() == 'spielplan') { ?>
            <?= Html::link($turnier->get_spielplan_link('tc'), 'Ergebnisse eintragen', true, 'launch') ?>
        <?php }// endif?>
        <?php if (isset($_SESSION['logins']['la']) && !(Helper::$ligacenter ?? false)) { ?>
            <?= Html::link($turnier->get_spielplan_link('lc'), 'Ergebnisse eintragen (Ligaausschuss)', true, 'launch') ?>
        <?php }// endif?>
        <?php if (isset($_SESSION['logins']['la'])) { ?>
            <?= Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier->get_turnier_id(), 'Turnierreport ausfüllen (Ligaausschuss)', true, 'launch') ?>
        <?php }// endif?>
    </div>
    <p>
        <b>
            <?= Html::link('https://einrad.hockey/spielplan_augustdorf.pdf', 'PDF-Spielplan', true, 'download')?>
        </b>
    </p>
<?php
if (Env::ACTIVE_FINAL_DISCORD) {
    include '../../templates/spielplan/spielplan_discord_read.tmp.php'; // Spiele
}
?>
    <div class="w3-center">
        <iframe
                width="100%"
                height="100%"
                class="w3-border-primary w3-card-4"
                style="border:5px solid;"
                src="https://docs.google.com/spreadsheets/d/e/2PACX-1vS8ie9atLJjIc9yhgsZRB8jRXSKoeioikMSW0kvfsTT7_qCvXEx51eaG-D_hRFo7eiam5R9pBs8usBA/pubhtml?gid=381383901&amp;single=true&amp;widget=true&amp;headers=true"></iframe>
    </div>
<?php include '../../templates/footer.tmp.php';

