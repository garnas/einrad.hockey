<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
Html::$titel = "Dokumente | Deutsche Einradhockeyliga";
Html::$content = 'Die wichtigsten Dokumente für den Betrieb der deutschen Einradhockeyliga können hier eingesehen werden.';
include '../../templates/header.tmp.php';
?>
<h1 class="w3-text-grey">Dokumente</h1>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Modus</h2>
    <p class="w3-text-grey w3-border-grey w3-border-top">Saison <?= Html::get_saison_string() ?></p>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_MODUS ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i>
            Ligamodus (Änderungen noch bis Saisonstart wahrscheinlich, insbesondere bei den Finalturnieren)
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_TURNIER ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i>
            Turniermodi
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_MODUS_KURZ ?>">
        <p>
            <i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file</i>
            Zusammenfassung Ligamodus - nicht aktuell
        </p>
    </a>
    <p class="w3-text-grey w3-border-grey w3-border-bottom w3-right-align">28.06.2022</p>
</div>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Finale der Deutschen Einradhockeyliga</h2>
    <p class="w3-text-grey w3-border-grey w3-border-top">Saison <?= Html::get_saison_string() ?></p>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_FINALE ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            Turniermodus des Finales der Deutschen Einradhockeyliga - nicht aktuell
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_FINALE_MODI ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            Spielpläne Finalmodi - nicht aktuell
        </p>
    </a>
    <p class="w3-text-grey w3-border-grey w3-border-bottom w3-right-align">08.07.2021</p>
</div>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Regeln</h2>
    <p class="w3-text-grey w3-border-grey w3-border-top">Saison <?= Html::get_saison_string() ?></p>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_REGELN ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            Regelwerk
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_REGELN_KURZ ?>">
        <p>
            <i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file
            </i>
            Zusammenfassung Regelwerk
        </p>
    </a>
    <p class="w3-text-grey w3-border-grey w3-border-bottom w3-right-align">14.08.2020</p>
</div>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Englisch</h2>
    <p class="w3-text-grey w3-border-grey w3-border-top">Saison <?= Html::get_saison_string() ?></p>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_REGELN_IUF ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            IUF-Rules (only
            international competitions)
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_MODUS_KURZ_ENG ?>">
        <p>
            <i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file
            </i>
            Summary League Mode
        </p>
    </a>
    <p class="w3-text-grey w3-border-grey w3-border-bottom w3-right-align">14.08.2020</p>
</div>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Sonstiges</h2>
    <p class="w3-text-grey w3-border-grey w3-border-top">Saison <?= Html::get_saison_string() ?></p>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_CHECK_PDF ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            Checkliste für Ausrichter (pdf)
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_CHECK_XLSX ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            Checkliste für Ausrichter (xlsx)
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_HYGIENE ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            Empfohlenes Hygienekonzept
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_DSGVO ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            Datenschutzhinweise
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_SPIELPLAENE_ALT ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            alte Spielplanvorlagen
        </p>
    </a>
    <p class="w3-text-grey w3-border-grey w3-border-bottom w3-right-align">07.10.2021</p>
</div>

<?php include '../../templates/footer.tmp.php';
