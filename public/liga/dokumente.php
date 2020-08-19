<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
$titel = "Dokumente | Deutsche Einradhockeyliga";
$content = 'Die wichtigsten Dokumente für den Betrieb der deutschen Einradhockeyliga können hier eingesehen werden.';
include '../../templates/header.tmp.php';
?>
<h1 class="w3-text-grey">Dokumente</h1>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Modus</h2>
    <p class="w3-text-grey w3-border-grey w3-border-top">Saison <?=Form::get_saison_string()?></p>
    <a class="no w3-hover-text-secondary" href="<?=Config::LINK_MODUS?>"><p><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Ligamodus</p></a>
    <a class="no w3-hover-text-secondary" href="<?=Config::LINK_TURNIER?>"><p><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Turniermodi</p></a>
    <a class="no w3-hover-text-secondary" href="<?=Config::LINK_MODUS_KURZ?>"><p><i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file</i> Zusammenfassung Ligamodus</p></a>
    <p class="w3-text-grey w3-border-grey w3-border-bottom w3-right-align">14.08.2020</p>
</div>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Regeln</h2>
    <p class="w3-text-grey w3-border-grey w3-border-top">Saison <?=Form::get_saison_string()?></p>
    <a class="no w3-hover-text-secondary" href="<?=Config::LINK_REGELN?>"><p><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Regelwerk</p></a>
    <a class="no w3-hover-text-secondary" href="<?=Config::LINK_REGELN_KURZ?>"><p><i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file</i> Zusammenfassung Regelwerk</p></a>
    <p class="w3-text-grey w3-border-grey w3-border-bottom w3-right-align">14.08.2020</p>
</div>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Englisch</h2>
    <p class="w3-text-grey w3-border-grey w3-border-top">Saison <?=Form::get_saison_string()?></p>
    <a class="no w3-hover-text-secondary" href="<?=Config::LINK_REGELN_IUF?>"><p><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> IUF-Rules (only international competitions)</p></a>
    <a class="no w3-hover-text-secondary" href="<?=Config::LINK_MODUS_KURZ_ENG?>"><p><i class="w3-xxlarge w3-text-tertiary material-icons">insert_drive_file</i> Summary League Mode</p></a>
    <p class="w3-text-grey w3-border-grey w3-border-bottom w3-right-align">14.08.2020</p>
</div>
<div class="w3-panel w3-card">
    <h2 class="w3-text-primary">Sonstiges</h2>
    <p class="w3-text-grey w3-border-grey w3-border-top">Saison <?=Form::get_saison_string()?></p>
    <a class="no w3-hover-text-secondary" href="<?=Config::LINK_DSGVO?>"><p><i class="w3-xxlarge w3-text-primary material-icons">insert_drive_file</i> Datenschutzhinweise</p></a>
    <p class="w3-text-grey w3-border-grey w3-border-bottom w3-right-align">19.08.2020</p>
</div>

<?php include '../../templates/footer.tmp.php';