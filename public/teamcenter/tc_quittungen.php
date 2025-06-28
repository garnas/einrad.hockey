<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;
use App\Service\Turnier\TurnierService;
use App\Repository\Turnier\TurnierRepository;
use Mpdf\HTMLParserMode;

require_once '../../init.php'; // autoloader und Session
require_once '../../logic/session_team.logic.php'; // Auth


$teamId = $_SESSION['logins']['team']['id'] ?? 0;
$turnierId = (int) @$_GET['turnier_id'];
$turnier = TurnierRepository::get()->turnier($turnierId);

$team_von = $turnier->getAusrichter();
$team_fuer = TeamRepository::get()->team(16);

// Besteht die Berechtigung das Turnier zu bearbeiten?
if (Helper::$teamcenter && !TurnierService::isAusrichter($turnier, $teamId)){
    Html::error("Keine Berechtigung Quittungen zu erstellen.");
    Helper::reload('/liga/turniere.php');
}

ob_start();
include "../css/pdf.css.php"; // Es wurde eine eigene Css-Datei für die PDF-Erstellung erstellt, da der Css-Code nicht immer kompatibel mit mpdf war
$css_style = ob_get_clean();


$mpdf = MPDF::load_mpdf(); // Erstellt ein MPDF-Objekt aus dem Framework
$mpdf->shrink_tables_to_fit = 4;
$mpdf->SetTitle('Quittungen ' . e($turnier->getDetails()->getOrt()));
$mpdf->SetHTMLHeader('<img src="../bilder/logo_lang_small.png" style="margin-top:18px; width: 70mm; float: right;">');
$mpdf->WriteHTML($css_style, HTMLParserMode::HEADER_CSS);

$today = new DateTime("today");
if ($turnier->getDatum() > $today) {
    $datum_unterschift = $turnier->getDatum();
} else {
    $datum_unterschift = $today;
}
$teams = TurnierService::getSetzListe($turnier);
$is_first_page = true;
foreach ($teams as $team_fuer_liste) {
    $team_fuer = $team_fuer_liste->getTeam();
    if ($team_fuer->id() == $team_von->id()) {
        continue;
    }
    $html = ob_start();
    ?>
    <div style="color: #3a3a3a; font-size: 16px">
        <h1 class="w3-center" style="padding-top: 64px">Quittung: <?= e($team_fuer->getName()) ?></h1>
        <div>
            <p>
                <b>Betrag</b>
                <br>
                <?=e($turnier->getDetails()->getStartgebuehr())?>
                <i>(In Worten: <?=Helper::zahlen_ausschreiben(e($turnier->getDetails()->getStartgebuehr()))?>)</i>
            </p>
        </div>
        <div style="padding-top: 56px">
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
            Team: <?= str_replace("*", "", e($team_fuer->getName()), ) ?>
            <?php if ($team_fuer->isLigaTeam() && $team_fuer->getDetails()->getVerein()): ?>
                <i>(Verein: <?=e($team_fuer->getDetails()->getVerein())?>)</i>
            <?php endif; ?>
        </div>
        <div style="padding-top: 24px">
            <b>Zweck</b>
            <br>
            Startgebühr Ligaturnier <?= (e($turnier->getName() ?? ' ')) . e($turnier->getDetails()->getOrt()) . " am " . e($turnier->getDatum()->format("d.m.Y")) ?>
        </div>
        <div style="padding-top: 64px">
            <b>Ort & Datum</b>
            <br>
            <?= e($turnier->getDetails()->getOrt()) ?> am <?= $datum_unterschift->format("d.m.Y")?>
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
    if ($is_first_page) {
        $is_first_page = false;
    } else {
        $mpdf->AddPage();
    }
    $mpdf->WriteHTML($html, HTMLParserMode::HTML_BODY);

}

// Output - Otpion 'D' für Download, 'I' für im Browser anzeigen
$mpdf->Output('Quittungen '. e($turnier->getDetails()->getOrt()) . '.pdf', 'I');
