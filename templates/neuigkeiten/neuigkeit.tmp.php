<?php 
    use App\Service\Neuigkeit\FormatService; 
    use App\Service\Neuigkeit\ColorService; 
    use App\Enum\Neuigkeit\NeuigkeitArt;
?>

<div class='w3-panel w3-card-4 w3-responsive <?=$neuigkeit->getTitel() ? "" : "w3-topbar w3-border-primary"?>'>
    <?php if ($neuigkeit->getTitel()): ?>
        <!-- Überschrift -->
        <h3 class="w3-center w3-padding-small <?=ColorService::getColor($neuigkeit->getArt())?>" style="margin: 0 -16px;">
            <?= e($neuigkeit->getTitel()) ?>
        </h3>
    <?php endif; ?>

    <?php if ($neuigkeit->getLinkJpg() != ''): ?>
        <!-- Bild -->
        <div class='w3-section w3-center w3-stretch w3-light-grey'>
            <a href='<?= $neuigkeit->getBildVerlinken() ?: e($neuigkeit->getLinkJpg()) ?>'>
                <img class='w3-image w3-hover-opacity' alt="Bild für die Neuigkeit '<?= e($neuigkeit->getTitel() ?? "Ohne Titel") ?>' " src=<?= e($neuigkeit->getLinkJpg()) ?>>
            </a>
        </div>
    <?php endif; ?>

    <!-- Text -->
    <div class="w3-section">
        <?= nl2br(e($neuigkeit->getInhalt())) ?>
    </div>
    
    <?php if (e($neuigkeit->getLinkPdf()) != ''): ?>
        <!-- PDF -->
        <?= Html::link(e($neuigkeit->getLinkPdf()), "PDF-Anhang", true, "insert_drive_file") ?>
    <?php endif; ?>

    <?php if ($neuigkeit->isAktiv()): ?>
        <div class='w3-section w3-text-grey'>
            <div class="w3-row w3-hide-medium w3-hide-small">
                <div class="w3-col l6 w3-left-align"><?= Html::icon("create") ?> <?= e($neuigkeit->getEingetragenVon()) ?></div>
                <div class="w3-col l6 w3-right-align"><?= Html::icon("schedule") ?> <?= FormatService::getTimespan($neuigkeit->getZeit()) ?></div>
            </div>
            <div class="w3-row w3-hide-large">
                <div class="w3-right-align"><?= Html::icon("schedule") ?> <?= FormatService::getTimespan($neuigkeit->getZeit()) ?></div>
                <div class="w3-right-align"><?= Html::icon("create") ?> <?= e($neuigkeit->getEingetragenVon()) ?></div>
            </div>
        </div>
    <?php else: ?>
        <div class='w3-section w3-text-grey'>
            <div class="w3-row w3-hide-medium w3-hide-small">
                <div class="w3-col l6 w3-left-align"><?= Html::icon("create") ?> <?= e($neuigkeit->getEingetragenVon()) ?></div>
                <div class="w3-col l3 w3-right-align"><?= Html::icon("archive") ?> Archiviert</div>
                <div class="w3-col l3 w3-right-align"><?= Html::icon("schedule") ?> <?= FormatService::getTimespan($neuigkeit->getZeit()) ?></div>
            </div>
            <div class="w3-row w3-hide-large">
                <div class="w3-right-align"><?= Html::icon("schedule") ?> <?= FormatService::getTimespan($neuigkeit->getZeit()) ?></div>
                <div class="w3-right-align"><?= Html::icon("archive") ?> Archiviert</div>
                <div class="w3-right-align"><?= Html::icon("create") ?> <?= e($neuigkeit->getEingetragenVon()) ?></div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (LigaLeitung::is_logged_in("ligaausschuss")): ?>
        <!-- Darstellungen des Ligaausschusses; Die Berechtigung wird nicht geprüft -->
        <hr>
        <div class="w3-section">
            <div class="w3-row w3-right-align">
                <?php if ($neuigkeit->isAktiv()): ?>
                    <a class='no w3-button w3-light-grey' href="../ligacenter/lc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?= $neuigkeit->getNeuigkeitenId() ?>"><?= Html::icon("create") ?> Bearbeiten</a>
                    <a class='no w3-button w3-light-grey' href="../ligacenter/lc_neuigkeit_archivieren.php?neuigkeiten_id=<?= $neuigkeit->getNeuigkeitenId() ?>"><?= Html::icon("archive") ?> Archivieren</a>
                <?php endif; ?>
                <a class='no w3-button w3-secondary' href="../ligacenter/lc_neuigkeit_loeschen.php?neuigkeiten_id=<?= $neuigkeit->getNeuigkeitenId() ?>" ><?= Html::icon("delete") ?> Löschen</a>
                <br><span class="w3-tiny w3-text-gray">als Ligaausschuss</span>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (LigaLeitung::is_logged_in("team_social_media")): ?>
        <!-- Darstellungen des Öffentlichkeitsausschusses; Die Berechtigung wird nicht geprüft -->
        <hr>
        <div class="w3-section">
            <div class="w3-row w3-right-align">
                <?php if ($neuigkeit->isAktiv()): ?>
                    <a class='no w3-button w3-light-grey' href="../oefficenter/oc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?= $neuigkeit->getNeuigkeitenId() ?>"><?= Html::icon("create") ?> Bearbeiten</a>
                    <a class='no w3-button w3-light-grey' href="../oefficenter/oc_neuigkeit_archivieren.php?neuigkeiten_id=<?= $neuigkeit->getNeuigkeitenId() ?>"><?= Html::icon("archive") ?> Archivieren</a>
                <?php endif; ?>
                <a class='no w3-button w3-secondary' href="../oefficenter/oc_neuigkeit_loeschen.php?neuigkeiten_id=<?= $neuigkeit->getNeuigkeitenId() ?>" ><?= Html::icon("delete") ?> Löschen</a>
            </div>
            <br><span class="w3-tiny w3-text-gray">als Öffentlichkeitsausschuss</span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['logins']['team']['name']) && $_SESSION['logins']['team']['name'] === $neuigkeit->getEingetragenVon()): ?>
        <!-- Darstellungen des Teams; Die Berechtigung wird nicht geprüft -->
        <hr>
        <div class="w3-section">
            <div class="w3-row w3-right-align">
                <?php if ($neuigkeit->isAktiv()): ?>
                    <a class='no w3-button w3-light-grey' href="../teamcenter/tc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?= $neuigkeit->getNeuigkeitenId() ?>"><?= Html::icon("create") ?> Bearbeiten</a>
                    <a class='no w3-button w3-light-grey' href="../teamcenter/tc_neuigkeit_archivieren.php?neuigkeiten_id=<?= $neuigkeit->getNeuigkeitenId() ?>"><?= Html::icon("archive") ?> Archivieren</a>
                <?php endif; ?>
                <a class='no w3-button w3-secondary' href="../teamcenter/tc_neuigkeit_loeschen.php?neuigkeiten_id=<?= $neuigkeit->getNeuigkeitenId() ?>" ><?= Html::icon("delete") ?> Löschen</a>
            </div>
        </div>
    <?php endif; ?>
</div>