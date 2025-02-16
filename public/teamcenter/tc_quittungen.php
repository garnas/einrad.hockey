<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;
use App\Service\Turnier\TurnierService;
use App\Repository\Turnier\TurnierRepository;

require_once '../../init.php'; // autoloader und Session
require_once '../../logic/session_team.logic.php'; // Auth


$teamId = $_SESSION['logins']['team']['id'] ?? 0;
$turnierId = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnierId);

$team_von = $turnier->getAusrichter();
$team_fuer = TeamRepository::get()->team(16);

// Besteht die Berechtigung das Turnier zu bearbeiten?
//if (Helper::$teamcenter && !TurnierService::isAusrichter($turnier, $teamId)){
//    Html::error("Keine Berechtigung das Turnier zu bearbeiten");
//    Helper::reload('/liga/turniere.php');
//}
ob_start();
include "../css/pdf.css.php"; // Es wurde eine eigene Css-Datei für die PDF-Erstellung erstellt, da der Css-Code nicht immer kompatibel mit mpdf war
$css_style = ob_get_clean();

$html = ob_start();
?>
<div>
    <h1 class="w3-center" style="padding-top: 64px">Quittung für <?= e($team_fuer->getName()) ?></h1>
    <div>
        <p>
            <b>Betrag:</b>
            <br>
            <?=e($turnier->getDetails()->getStartgebuehr())?>
            <i>(In Worten: <?=Helper::zahlen_ausschreiben(e($turnier->getDetails()->getStartgebuehr()))?>)</i>
        </p>
    </div>
    <div style="padding-top: 24px">
        <b>Von</b>
        <br>
        <?=e($turnier->getDetails()->getOrganisator())?>
        <br>
        Ausrichter: <?= e($team_von->getName()) ?>
        <?php if ($team_von->getDetails()->getVerein()): ?>
            <i>(Verein: <?=e($team_von->getDetails()->getVerein())?>)</i>
        <?php endif; ?>
    </div>

    <div style="padding-top: 24px">
        <b>Für</b>
        <br>
        Team: <?= e($team_fuer->getName()) ?>
        <?php if ($team_fuer->getDetails()->getVerein()): ?>
            <i>(Verein: <?=e($team_fuer->getDetails()->getVerein())?>)</i>
        <?php endif; ?>
    </div>
    <div style="padding-top: 24px">
        <b>Zweck</b>
        <br>
        Startgebühr Ligaturnier <?= (e($turnier->getName() ?? ' ')) . e($turnier->getDetails()->getOrt()) . " am " . e($turnier->getDatum()->format("d.m.Y")) ?>
    </div>
    <div style="padding-top: 24px">
        <b>Ort & Datum</b>
        <br>
        <?= e($turnier->getDetails()->getOrt()) ?> am <?= date("d.m.Y")?>
    </div>
    <div  style="padding-top: 24px">
        <b>Unterschrift</b>
        <div  style="padding-top: 64px">
        <i><?=e($turnier->getDetails()->getOrganisator())?></i>
        </div>
    </div>
</div>

<?php
$html = ob_get_clean();

//include '../../templates/header.tmp.php';
//echo $html;
//exit();
// Formularauswertung
$mpdf = MPDF::load_mpdf(); // Erstellt ein MPDF-Objekt aus dem Framework
$mpdf->shrink_tables_to_fit = 4;
$mpdf->SetTitle('Quittung ' . e($turnier->getDetails()->getOrt()));
$mpdf->SetHTMLHeader('<img src="../bilder/logo_lang_small.png" style="margin-top:18px; width: 70mm; float: right;">');
$mpdf->WriteHTML($css_style,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html,\Mpdf\HTMLParserMode::DEFAULT_MODE);

// Output - Otpion 'D' für Download, 'I' für im Browser anzeigen
$mpdf->Output('Quittung '. e($team_fuer->getName()) . '.pdf', 'I');
