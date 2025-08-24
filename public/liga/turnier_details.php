<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Turnier\TurnierRepository;
use App\Service\Team\TeamSnippets;
use App\Service\Turnier\TurnierLinks;
use App\Service\Turnier\TurnierService;
use App\Service\Turnier\TurnierSnippets;

require_once '../../init.php';

$turnier_id = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnier_id);

if ($turnier === null){
    Helper::not_found("Das Turnier konnte nicht gefunden werden.");
}

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = $turnier->getName() ?: $turnier->getDetails()->getOrt() ." | Deutsche Einradhockeyliga";
Html::$content = "Alle wichtigen Turnierdetails werden hier angezeigt.";
include '../../templates/header.tmp.php';
?>

<!-- Überschrift -->
<h1 class="w3-text-primary">
    <span class="w3-text-grey"><i style="font-size: 31px; vertical-align: -19%;" class="material-icons">info</i> Turnierinfos:</span>
    <br><?= TurnierSnippets::nameBrTitel($turnier)?>
</h1>

<!-- Anzeigen der allgemeinen Infos -->
<p class="w3-text-grey w3-border-bottom w3-border-grey">Allgemeine Infos</p>  
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr style="white-space: nowrap;">
            <td class="w3-primary" style="vertical-align: middle; width: 100px;"><i class="material-icons">map</i> Adresse</td>
            <td>
                <?= e($turnier->getDetails()->getHallenname())?><br>
                <?= e($turnier->getDetails()->getStrasse())?><br>
                <?= e($turnier->getDetails()->getPlz() .' '.$turnier->getDetails()->getOrt())?><br>
                <?= Html::link(
                        str_replace(' '
                            , '%20'
                            , 'https://www.google.de/maps/search/'
                                .       e($turnier->getDetails()->getHallenname())
                                . "+" . e($turnier->getDetails()->getStrasse())
                                . "+" . e($turnier->getDetails()->getPlz())
                                . "+" . e($turnier->getDetails()->getOrt())
                                . '/'),
                        'Google Maps',
                        true,
                        'launch') ?>
                <?php if (!empty($turnier->getDetails()->getHaltestellen())): ?>
                    <p style="white-space: normal;">
                        <i>Haltestellen: <?= e($turnier->getDetails()->getHaltestellen()) ?></i>
                    </p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;">
                <i class="material-icons">schedule</i> Beginn
            </td>
            <td>
                <?=$turnier->getDetails()->getStartzeit()->format("H:i")?>&nbsp;Uhr
                <?php if ($turnier->hasBesprechung()): ?>
                    <p>
                        <i>Alle Teams sollen sich um <?= $turnier->getDetails()->getBesprechungUhrzeit() ?> Uhr zu einer gemeinsamen Turnierbesprechung einfinden.</i>
                    </p>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">mail</i> Kontakt</td>
            <td>
                <p>
                    <i>Ausrichter:</i>
                    <br>
                    <?= TeamSnippets::getEmailLink($turnier->getAusrichter()) ?: e($turnier->getAusrichter()->getName())?>
                </p> 
                <p><i>Organisator:</i><br><?= e($turnier->getDetails()->getOrganisator()) ?></p>
                <p><i>Handy:</i><br><?= TurnierSnippets::getHandy($turnier) ?></p>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">payments</i> Startgebühr</td>
            <td><?= e($turnier->getDetails()->getStartgebuehr()) ?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="white-space: nowrap; vertical-align: middle;"><i class="material-icons">announcement</i> Hinweis</td>
            <td><?=nl2br(e($turnier->getDetails()->getHinweis()))?></td>
        </tr>
    </table>
</div>

<!--Anmeldungen / Listen -->
<?= TurnierSnippets::getListen($turnier) ?>

<!-- Anzeigen der Ligaspezifischen Infos -->
<p class="w3-text-grey w3-margin-top w3-border-bottom w3-border-grey">Ligaspezifische Infos</p> 
<div class="w3-responsive w3-card">
    <table class="w3-table w3-striped">
        <tr>
            <td class="w3-primary" style="vertical-align: middle; width: 20px;">Turnier-ID</td>
            <td><?= $turnier->id()?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Phase</td>
            <td><?= TurnierSnippets::translate($turnier->getPhase()) ?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Losung</td>
            <td>
                <?= ($turnier->isLigaturnier())
                    ? TurnierService::getLosDatum($turnier)
                    : '--' ?>
                (Übergang von Wartephase zur Setzphase)
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Spieltag</td>
            <td><?= $turnier->getSpieltag() ?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Art</td>
            <td><?= TurnierSnippets::translate($turnier->getArt()) ?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Turnierblock</td>
            <td>
                <?= $turnier->getBlock() ?>
                <?php if ($turnier->isSofortOeffnen() && $turnier->isWartePhase()): ?>
                    <br>
                    <span class="w3-text-grey">Das Turnier wird direkt nach dem Phasenwechsel auf ABCDEF geöffnet</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Plätze</td>
            <td><?= $turnier->getDetails()->getPlaetze() ?></td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Min. Teams</td>
            <td>
                <?php if ($turnier->getDetails()->getMinTeams()): ?>
                <?= e($turnier->getDetails()->getMinTeams()) ?>
                <span class="w3-text-grey">(Das Turnier findet ab dieser Anzahl an Teams auf der Setzliste statt.)</span>
                <?php endif; ?>
            </td>
        </tr>
        <tr>
            <td class="w3-primary" style="vertical-align: middle">Erstellt am</td>
            <td><?= $turnier->getErstelltAm()->format("d.m.Y") ?></td>
        </tr>
    </table>
</div>

<!-- Weiterführende Links -->
<p class="w3-text-grey w3-border-bottom w3-border-grey">Links</p>
<p><?=Html::link('../liga/turniere.php#' . $turnier_id, 'Anstehende Turniere', icon: "event")?></p>
<?php if($turnier->isSpielplanPhase()){?>
    <p><?=Html::link(TurnierLinks::spielplan($turnier), 'Zum Spielplan', true, "reorder")?></p>
<?php }//end if?>

<?php if (isset($_SESSION['logins']['team'])){?>
    <p><?=Html::link('../teamcenter/tc_team_anmelden.php?turnier_id=' . $turnier_id, 'Zum Turnier anmelden', icon: "how_to_reg")?></p>
    <p><?=Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, 'Zum Turnierreport', icon: "article")?></p>
<?php }else{ ?>
    <p><?=Html::link('../teamcenter/tc_turnier_report.php?turnier_id=' . $turnier_id, 'Zum Turnierreport', icon: "lock")?></p>
<?php } //endif?>

<?php if (TurnierService::isAusrichter($turnier, $_SESSION['logins']['team']['id'] ?? 0)) {?>
    <p><?=Html::link('../teamcenter/tc_turnier_bearbeiten.php?turnier_id=' . $turnier_id, 'Turnier als Ausrichter bearbeiten', icon: "create")?></p>
<?php } //endif?>

<?php if (isset($_SESSION['logins']['la'])){?>
    <p><?=Html::link('../ligacenter/lc_turnier_bearbeiten.php?turnier_id=' . $turnier_id, 'Turnier bearbeiten (Ligaausschuss)')?></p>
    <p><?=Html::link('../ligacenter/lc_team_anmelden.php?turnier_id=' . $turnier_id, 'Teams anmelden (Ligaausschuss)')?></p>
    <p><?=Html::link('../ligacenter/lc_turnier_log.php?turnier_id=' . $turnier_id, 'Turnierlog einsehen (Ligaausschuss)')?></p>
    <p><?=Html::link('../ligacenter/lc_turnier_report.php?turnier_id=' . $turnier_id, 'Zum Turnierreport (Ligaausschuss)', icon: "article")?></p>
<?php } //endif?>

<?php include '../../templates/footer.tmp.php';