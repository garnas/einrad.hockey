<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Turnier\TurnierRepository;
use App\Service\Turnier\BlockService;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';

$turniere = TurnierRepository::getKommendeTurniere();


// Turnierbesprechung




/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Turnierliste | Deutsche Einradhockeyliga";
//Html::$page_width = "800px";
Html::$content = "Eine Liste aller ausstehenden Spaß-, Final- und Ligaturniere der Deutschen Einradhockeyliga.";
include '../../templates/header.tmp.php';
?>

    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
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

    <h1 class="w3-text-primary">Turniere der Saison <?= Html::get_saison_string() ?></h1>

    <!-- Turnier suchen -->
    <div class="w3-section w3-text-grey w3-border-bottom" style="width: 250px;">
        <label for="myInput"><?= Html::icon("search") ?></label>
        <input id="myInput" class='w3-padding w3-border-0' style="width: 225px;" type="text" placeholder="Turnier suchen">
    </div>

    <?php 
    if (empty($turniere)):
        Html::message('info', "Keine Turniere gefunden.", NULL);
    endif; 
    ?>

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
                    <div class="w3-center">
                        <?php if ($turnier->isFinalTurnier()): ?>
                            <h3 class='w3-text-secondary'>
                                <?= $turnier->getName() ?>
                            </h3>
                            <h4>
                                <?= TurnierSnippets::datum($turnier) ?> <span class="w3-text-primary"><?= e($turnier->getDetails()->getOrt()) ?></span>
                            </h4>
                        <?php else: ?>
                            <h3 class='w3-text-grey'>
                                <?= $turnier->getName()?>
                            </h3>
                            <h4>
                                <?= TurnierSnippets::datum($turnier) ?>
                                <span class="w3-text-primary">
                                    <?= e($turnier->getDetails()->getOrt()) ?>
                                </span>
                                <?= BlockService::toString($turnier->getBlock()) ?>
                            </h4>
                        <?php endif; ?>
                    </div>
                    <div style="font-size: 13px;" class="w3-text-grey">
                        <i class='w3-display-topleft w3-padding'><?= TurnierSnippets::status($turnier) ?></i>
                        <i class='w3-display-bottomleft w3-padding'><?= TurnierSnippets::phase($turnier) ?></i>
                        <i class='w3-display-topright w3-padding'>
                            <?= TurnierSnippets::plaetze($turnier) ?>
                        </i>
                        <i class='w3-display-bottomright w3-padding'><?= e($turnier->getAusrichter()->getName()) ?></i>
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
                                    <td class="w3-text-primary" style="width: 150px"><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), '<i class="material-icons">pending</i> Plätze') ?></td>
                                    <td><?= $turnier->getDetails()->getPlaetze() ?></td>
                                </tr>
                                <tr style="white-space: nowrap;">
                                    <td class="w3-text-primary"><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), '<i class="material-icons">schedule</i> Beginn') ?></td>
                                    <td><?= $turnier->getDetails()->getStartzeit()->format("H:i") ?> Uhr
                                        <?php if ($turnier->hasBesprechung()): ?>
                                            <i>Gemeinsame Teambesprechung um <?= $turnier->getDetails()->getBesprechungUhrzeit() ?>&nbsp;Uhr</i>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                                <tr style="white-space: nowrap;">
                                    <td class="w3-text-primary" style=""><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), '<i class="material-icons">event</i> Wochentag') ?></td>
                                    <td><?= TurnierSnippets::wochentag($turnier)?></td>
                                </tr>
                                <tr>
                                    <td style="white-space: nowrap; vertical-align: middle;" class="w3-text-primary"><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), '<i class="material-icons">announcement</i> Hinweis') ?></td>
                                    <td style="white-space: normal"><?= nl2br(e($turnier->getDetails()->getHinweis())) ?></td>
                                </tr>
                                <?php if($turnier->isWartePhase() && $turnier->isLigaturnier()): ?>
                                    <tr style="white-space: nowrap;">
                                        <td class="w3-text-primary" style=""><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), '<i class="material-icons">event</i> Phasenwechsel') ?></td>
                                        <td><?= TurnierService::getLosDatum($turnier)?> (Loszeitpunkt)</td>
                                    </tr>
                                <?php endif; ?>
                                <?php if($turnier->isLigaturnier()): ?>
                                    <tr style="white-space: nowrap;">
                                        <td class="w3-text-primary" style=""><?= Html::link('../liga/turnier_details.php?turnier_id=' . $turnier->id(), '<i class="material-icons">event</i> Abmeldefrist') ?></td>
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
                                <p><?= Html::link($turnier->getSpielplanDatei() ?? ('../liga/spielplan.php?turnier_id=' . $turnier->id()), 'Zum Spielplan', icon:'reorder') ?></p>
                            <?php } //endif?>
                            <?php if (isset($_SESSION['logins']['team'])) { ?>
                                <p><?= Html::link('../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier->id(), 'Zur Anmeldeseite', icon:'how_to_reg') ?></p>
                                <p><?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->id(), 'Zum Turnierreport', icon:'article') ?></p>
                            <?php } else { ?>
                                <p><?= Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier->id(), 'Zum Turnierreport', icon:'lock') ?></p>
                            <?php } //endif?>
                            <?php if (($_SESSION['logins']['team']['id'] ?? 0) === $turnier->getAusrichter()->id()) { ?>
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
