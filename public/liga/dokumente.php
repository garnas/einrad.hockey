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
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_MODUS_KURZ ?>">
        <p>
            <i class="w3-xxlarge w3-text-gray material-icons">insert_drive_file</i>
            Zusammenfassung Ligamodus
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_MODUS ?>?version=2025-07-13">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i>
            Ligamodus
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_TURNIER ?>?version=2024-07-25">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i>
            Turniermodi
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_SCHIRI_CHECKLIST ?>">
        <p>
            <i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file</i>
            Schiedsrichter Checkliste für praktische Prüfung
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_SCHIRI_LEITLINIE ?>">
        <p>
            <i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file</i>
            Schiedsrichter-Leitlinie für Spiele auf fortgeschrittenem Niveau
        </p>
    </a>
</div>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Spielregeln</h2>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_REGELN_KURZ ?>">
        <p>
            <i class="w3-xxlarge w3-text-grey material-icons">insert_drive_file
            </i>
            Die zehn wichtigsten Regeln
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_REGELN ?>">
        <p>
            <i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file
            </i>
            Offizielles Regelwerk der Deutschen Einradhockeyliga
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_REGELN_IUF ?>">
        <p>
            <i class="w3-xxlarge w3-text-grey material-icons">insert_drive_file
            </i>
            IUF-Rules (only international competitions)
        </p>
    </a>
</div>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Sonstiges</h2>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_CHECK_PDF ?>">
        <p>
            <i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file
            </i>
            Checkliste für Ausrichter (pdf)
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_CHECK_XLSX ?>">
        <p>
            <i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file
            </i>
            Checkliste für Ausrichter (xlsx)
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_DSGVO ?>">
        <p>
            <i class="w3-xxlarge w3-text-grey material-icons">insert_drive_file
            </i>
            Datenschutzhinweise
        </p>
    </a>
    <a class="no w3-hover-text-secondary" href="<?= Nav::LINK_SPIELPLAENE_ALT ?>">
        <p>
            <i class="w3-xxlarge w3-text-grey material-icons">insert_drive_file
            </i>
            alte Spielplanvorlagen
        </p>
    </a>
</div>

<?php include '../../templates/footer.tmp.php';
