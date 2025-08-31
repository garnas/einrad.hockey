<?php

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\BlockService;
use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';

$turniere = TurnierRepository::getKommendeTurniere();

if ($turniere->isEmpty()) {
    Html::info("Es stehen zurzeit keine Turniere aus.");
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Turnierliste | Deutsche Einradhockeyliga";
Html::$content = "Eine Liste aller ausstehenden Spaß-, Final- und Ligaturniere der Deutschen Einradhockeyliga.";
include '../../templates/header.tmp.php';
?>

    <script src="<?= Env::BASE_URL ?>/javascript/jquery.min.js?v=20250825"></script>
    <script>
        //Turnierergebnisse filtern
        $(document).ready(function () {
            $("#myInput").on("keyup", function () {
                var value = $(this).val().toLowerCase();
                $("#myDIV section").filter(function () {
                    $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1)
                });
            });
        });
    </script>

    <?php //include '../../templates/finalturniere22.tmp.php'; ?>

    <div class='w3-card w3-panel w3-leftbar w3-border-yellow w3-pale-yellow'>
        <h3>Kadertermine</h3>
        <div class='w3-section'>Die Termine der beiden Nationalkader sind hier einsehbar: <span><?= Html::link("kader.php", " Termine des A- und B-Kaders", false, "") ?></span></div>
    </div>
    <h1 class="w3-text-primary">
        <?= Html::icon("access_time", tag: "h1") ?> Kommende Turniere
        <br>
        <span class="w3-text-grey">
            Saison <?= Html::get_saison_string() ?>
        </span>
    </h1>

    <!-- Turnier suchen -->
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <label for="myInput" class="w3-left"><?= Html::icon("search", 70) ?></label>
        <input id="myInput"
               class='w3-padding w3-border-0'
               style="width: 225px; display: inline-block;"
               type="text"
               placeholder="Turnier suchen"
        >
    </div>

    <!-- zu durchsuchendes div -->
    <div id="myDIV">
        <!--Turnierpanels -->
        <?php foreach ($turniere as $turnier): ?>
            <section onclick="modal('modal<?= $turnier->id() ?>')"
                     class='w3-display-container w3-panel w3-card'
                     style='cursor: pointer;'
                     id='<?= $turnier->id() ?>'>
                <!-- Angezeigtes Turnierpanel -->
                <div class='w3-panel'>
                    <div class="w3-center w3-padding-16">
                        <?php if ($turnier->isFinalTurnier()): ?>
                            <h3 class='w3-text-secondary'>
                                <?= $turnier->getName() ?>
                            </h3>
                            <h4>
                            <?= TurnierSnippets::wochentag($turnier, short: true)?>, <?= TurnierSnippets::datum($turnier) ?><br>
                            <span class="w3-text-primary"><?= e($turnier->getDetails()->getOrt()) ?></span>
                            </h4>
                        <?php else: ?>
                            <p class='w3-text-grey'>
                                <?= e($turnier->getName())?>
                            </p>
                            <h4>
                                <?= TurnierSnippets::wochentag($turnier) ?>, <?= TurnierSnippets::datum($turnier) ?>
                                <span class="w3-text-primary">
                                    <?= e($turnier->getDetails()->getOrt()) ?>
                                </span>
                                <?= BlockService::toString($turnier) ?>
                            </h4>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 13px;" class="w3-text-grey">
                        <i class='w3-display-topleft w3-padding'><?= TurnierSnippets::status($turnier) ?></i>
                        <?php if ($turnier->isSpielplanPhase()): ?>
                            <i class='w3-display-bottomleft w3-padding'>
                                <?= Html::link(TurnierLinks::spielplan($turnier), "Spielplan") ?>
                            </i>
                        <?php else: ?>
                            <i class='w3-display-bottomleft w3-padding'><?= TurnierSnippets::phase($turnier) ?></i>
                        <?php endif; ?>
                        <i class='w3-display-topright w3-padding'>
                            <?= TurnierSnippets::plaetze($turnier) ?>
                        </i>
                        <i class='w3-display-bottomright w3-padding'><?= e($turnier->getAusrichter()?->getName()) ?></i>
                    </div>

                    <!-- Ausklappbarer Content -->
                    <div style='display: none' class='' id="modal<?= $turnier->id() ?>">
                        <!-- Listen -->
                        <?= TurnierSnippets::getListen($turnier) ?>

                        <!-- Turnierdetails -->
                        <p class="w3-text-grey w3-border-bottom w3-border-grey">Details</p>
                        <div class="w3-responsive w3-stretch">
                            <table class="w3-table">
                                <tr style="white-space: nowrap;">
                                    <td class="w3-text-primary"><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), 'Beginn', icon: "schedule") ?></td>
                                    <td>
                                        <?php if ($turnier->getDetails()->getStartzeit()): ?>
                                            <?= $turnier->getDetails()->getStartzeit()->format("H:i") ?> Uhr
                                        <?php endif; ?>
                                        <?php if ($turnier->hasBesprechung()): ?>
                                            <i>Gemeinsame Teambesprechung um <?= $turnier->getDetails()->getBesprechungUhrzeit() ?>&nbsp;Uhr</i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; vertical-align: middle;" class="w3-text-primary"><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), 'Hinweis', icon: "announcement") ?></td>
                                    <td style="white-space: normal"><?= nl2br(e($turnier->getDetails()->getHinweis())) ?></td>
                                </tr>
                                <?php if($turnier->isWartePhase() && $turnier->isLigaturnier()): ?>
                                    <tr style="white-space: nowrap;">
                                        <td class="w3-text-primary" style=""><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), 'Phasenwechsel', icon: "event") ?></td>
                                        <td>
                                            <?= TurnierService::getLosDatum($turnier)?> (Loszeitpunkt)
                                            <?php if ($turnier->isSofortOeffnen() && $turnier->isWartePhase()): ?>
                                                <br>
                                                <span class="w3-text-grey">Das Turnier wird direkt nach dem Phasenwechsel auf ABCDEF geöffnet</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($turnier->isLigaturnier()): ?>
                                    <tr style="white-space: nowrap;">
                                        <td class="w3-text-primary" style=""><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), 'Abmeldefrist', icon: "event") ?></td>
                                        <td><?= TurnierService::getAbmeldeFrist($turnier)?></td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </div>

                        <!-- Links -->
                        <div style="margin-bottom: 24px;">
                            <p class="w3-text-grey w3-border-bottom w3-border-grey">Links</p>
                            <p><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), 'Alle Turnierdetails', icon:'info') ?></p>
                            <?php if ($turnier->isSpielplanPhase()) { ?>
                                <p><?= Html::link(TurnierLinks::spielplan($turnier), 'Zum Spielplan', icon:'reorder') ?></p>
                            <?php } //endif?>
                            <?php if (isset($_SESSION['logins']['team'])) { ?>
                                <p><?= Html::link('../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier->id(), 'Zur Anmeldeseite', icon:'how_to_reg') ?></p>
                                <p><?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->id(), 'Zum Turnierreport', icon:'article') ?></p>
                            <?php } else { ?>
                                <p><?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->id(), 'Zum Turnierreport', icon:'lock') ?></p>
                            <?php } //endif?>
                            <?php if (($_SESSION['logins']['team']['id'] ?? 0) === $turnier->getAusrichter()?->id()) { ?>
                                <p><?= Html::link('../teamcenter/tc_turnier_bearbeiten.php?turnier_id=' . $turnier->id(), 'Turnier als Ausrichter bearbeiten', icon:'create') ?></p>
                            <?php } //endif?>
                            <?php if (isset($_SESSION['logins']['la'])) { ?>
                                <p><?= Html::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $turnier->id(), 'Turnier bearbeiten (Ligaausschuss)') ?></p>
                                <p><?= Html::link('../ligacenter/lc_team_anmelden.php?turnier_id=' . $turnier->id(), 'Teams anmelden (Ligaausschuss)') ?></p>
                                <p><?= Html::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $turnier->id(), 'Turnierlog einsehen (Ligaausschuss)') ?></p>
                                <p><?= Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier->id(), 'Zum Turnierreport (Ligaausschuss)', icon:'article') ?></p>
                            <?php } //endif?>
                        </div>
                    </div>
                </div>
            </section>
        <?php endforeach; ?>
    </div>
<?php include '../../templates/footer.tmp.php';
