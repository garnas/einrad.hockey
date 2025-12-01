<div class='w3-panel w3-card-4 w3-responsive <?=$neuigkeit['titel'] ? "" : "w3-topbar w3-border-primary"?>'>
    <?php if ($neuigkeit['titel']): ?>
        <!-- Überschrift -->
        <h3 class="w3-center w3-primary w3-padding-small" style="margin: 0 -16px;">
            <?= e($neuigkeit['titel']) ?>
        </h3>
    <?php endif; ?>

    <?php if ($neuigkeit['link_jpg'] != ''): ?>
        <!-- Bild -->
        <div class='w3-section w3-center w3-stretch w3-light-grey'>
            <a href='<?= $neuigkeit['bild_verlinken'] ?: e($neuigkeit['link_jpg']) ?>'>
                <img class='w3-image w3-hover-opacity' alt="Bild für die Neuigkeit '<?= e($neuigkeit['titel'] ?? "Ohne Titel") ?>' " src=<?= e($neuigkeit['link_jpg']) ?>>
            </a>
        </div>
    <?php endif; ?>

    <!-- Text -->
    <div class="w3-section">
        <?= nl2br($neuigkeit['inhalt']) ?>
    </div>
    
    <?php if (e($neuigkeit['link_pdf']) != ''): ?>
        <!-- PDF -->
        <?= Html::link(e($neuigkeit['link_pdf']), "PDF-Anhang", true, "insert_drive_file") ?>
    <?php endif; ?>

    <?php if ($neuigkeit['aktiv']): ?>
        <div class='w3-section w3-text-grey'>
            <div class="w3-row w3-hide-medium w3-hide-small">
                <div class="w3-col l6 w3-left-align"><?= Html::icon("create") ?> <?= e($neuigkeit['eingetragen_von']) ?></div>
                <div class="w3-col l6 w3-right-align"><?= Html::icon("schedule") ?> <?= $neuigkeit['zeit'] ?></div>
            </div>
            <div class="w3-row w3-hide-large">
                <div class="w3-right-align"><?= Html::icon("schedule") ?> <?= $neuigkeit['zeit'] ?></div>
                <div class="w3-right-align"><?= Html::icon("create") ?> <?= e($neuigkeit['eingetragen_von']) ?></div>
            </div>
        </div>
    <?php else: ?>
        <div class='w3-section w3-text-grey'>
            <div class="w3-row w3-hide-medium w3-hide-small">
                <div class="w3-col l6 w3-left-align"><?= Html::icon("create") ?> <?= e($neuigkeit['eingetragen_von']) ?></div>
                <div class="w3-col l3 w3-right-align"><?= Html::icon("archive") ?> Archiviert</div>
                <div class="w3-col l3 w3-right-align"><?= Html::icon("schedule") ?> <?= $neuigkeit['zeit'] ?></div>
            </div>
            <div class="w3-row w3-hide-large">
                <div class="w3-right-align"><?= Html::icon("schedule") ?> <?= $neuigkeit['zeit'] ?></div>
                <div class="w3-right-align"><?= Html::icon("archive") ?> Archiviert</div>
                <div class="w3-right-align"><?= Html::icon("create") ?> <?= e($neuigkeit['eingetragen_von']) ?></div>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (LigaLeitung::is_logged_in("ligaausschuss")): ?>
        <!-- Darstellungen des Ligaausschusses; Die Berechtigung wird nicht geprüft -->
        <hr>
        <div class="w3-section">
            <div class="w3-row w3-right-align">
                <?php if ($neuigkeit['aktiv']): ?>
                    <a class='no w3-button w3-light-grey' href="../ligacenter/lc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>"><?= Html::icon("create") ?> Bearbeiten</a>
                    <a class='no w3-button w3-light-grey' href="../ligacenter/lc_neuigkeit_archivieren.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>"><?= Html::icon("archive") ?> Archivieren</a>
                <?php endif; ?>
                <a class='no w3-button w3-secondary' href="../ligacenter/lc_neuigkeit_loeschen.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>" ><?= Html::icon("delete") ?> Löschen</a>
                <br><span class="w3-tiny w3-text-gray">als Ligaausschuss</span>
            </div>
        </div>
    <?php endif; ?>
    
    <?php if (LigaLeitung::is_logged_in("team_social_media")): ?>
        <!-- Darstellungen des Öffentlichkeitsausschusses; Die Berechtigung wird nicht geprüft -->
        <hr>
        <div class="w3-section">
            <div class="w3-row w3-right-align">
                <?php if ($neuigkeit['aktiv']): ?>
                    <a class='no w3-button w3-light-grey' href="../oefficenter/oc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>"><?= Html::icon("create") ?> Bearbeiten</a>
                    <a class='no w3-button w3-light-grey' href="../oefficenter/oc_neuigkeit_archivieren.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>"><?= Html::icon("archive") ?> Archivieren</a>
                <?php endif; ?>
                <a class='no w3-button w3-secondary' href="../oefficenter/oc_neuigkeit_loeschen.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>" ><?= Html::icon("delete") ?> Löschen</a>
            </div>
            <br><span class="w3-tiny w3-text-gray">als Öffentlichkeitsausschuss</span>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_SESSION['logins']['team']['name']) && $_SESSION['logins']['team']['name'] === $neuigkeit['eingetragen_von']): ?>
        <!-- Darstellungen des Teams; Die Berechtigung wird nicht geprüft -->
        <hr>
        <div class="w3-section">
            <div class="w3-row w3-right-align">
                <?php if ($neuigkeit['aktiv']): ?>
                    <a class='no w3-button w3-light-grey' href="../teamcenter/tc_neuigkeit_bearbeiten.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>"><?= Html::icon("create") ?> Bearbeiten</a>
                    <a class='no w3-button w3-light-grey' href="../teamcenter/tc_neuigkeit_archivieren.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>"><?= Html::icon("archive") ?> Archivieren</a>
                <?php endif; ?>
                <a class='no w3-button w3-secondary' href="../teamcenter/tc_neuigkeit_loeschen.php?neuigkeiten_id=<?= $neuigkeit['neuigkeiten_id'] ?>" ><?= Html::icon("delete") ?> Löschen</a>
            </div>
        </div>
    <?php endif; ?>
</div>