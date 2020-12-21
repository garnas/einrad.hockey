<?php
require_once '../../logic/first.logic.php'; // Autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php'; // Logic der Challenge

$spieler_id = $_GET['spieler_id'] ?? 0;

// Überprüfung, ob die SpielerID auch zum Team gehört
if (!in_array($_GET['spieler_id'], array_column($team_spielerliste, 'spieler_id'))) die("Ungültige Spieler-Id");

$urkunden_daten = $challenge->get_spieler_result($spieler_id);

if ($urkunden_daten['geschlecht'] == "m") {
    $anrede = "Der Spieler";
} else {
    $anrede = "Die Spielerin";
}

$css_style = '
.standard {
    position: absolute;
    font-size: 16px;
    font-family: Verdana;
    text-align: center;
    color: #6b7ebd;
}

.rahmen {
    background-color: #6b7ebd;
}
';

// Start der PDF Erstellung
$mpdf = PDF::start_mpdf();

// Darstellung der Elemente auf der Seite
$html = '
<div class="standard rahmen" style="top:       0mm; left:  0mm; height: 5mm;   width: 100%;    "></div>
<div class="standard rahmen" style="bottom:    0mm; left:  0mm; height: 5mm;   width: 100%;    "></div>
<div class="standard rahmen" style="top:       0mm; left:  0mm; height: 100%;  width: 5mm;     "></div>
<div class="standard rahmen" style="top:       0mm; right: 0mm; height: 100%;  width: 5mm;     "></div>

<div class="standard" style="top: 40mm; left: 0mm; width: 100%;">
    <div style="">' . $anrede . '</div>
    <div style="font-size: 48px; padding-bottom: 25mm;">' . $urkunden_daten['vorname'] . ' ' . $urkunden_daten['nachname'] . '</div>
    <div style="">erreichte bei der km-Challenge 2020</div>
    <div style="">vom 13.11.2020 - 20.12.2020 mit</div>
    <div style="font-size: 48px; padding-bottom: 35mm;">' . number_format($urkunden_daten['kilometer'], 1, ',', '.') . ' km' . '</div>
    <div style="">den</div>
    <div style="font-size: 48px;">' . $urkunden_daten['platz'] . '. Platz' . '</div>
</div>

<div class="standard" style="top: 240mm; left: 20mm; text-align: left; padding-top: 12px; padding-right: 10mm; font-size: 12px; border-top: 2px solid #6b7ebd;">
    <div style="">Philipp Gross</div>
    <div style="">stellv. für den Ligaausschuss</div>
</div>

<div class="standard" style="top: 240mm; right: 20mm; text-align: right; padding-top: 15px; font-size: 12px;">
    <div style="">21.12.2020</div>
    <div style="">Deutsche Einradhockeyliga</div>
</div>
';

// Titel des Reiters im Browser
$mpdf->SetTitle($urkunden_daten['vorname'] . ' ' . $urkunden_daten['nachname'] . ' - Urkunde km-Challenge 2020');

// Einfügen des Wasserzeichens
$mpdf->SetWatermarkImage('../bilder/logo_kurz_small.jpg', 0.1, 45, 'F');
$mpdf->showWatermarkImage = true;

// Einstellen der CSS- und HTML-Strings für die Erstellung des Dokuments
$mpdf->WriteHTML($css_style,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html,\Mpdf\HTMLParserMode::HTML_BODY);

// Output des Dokuments
$mpdf->Output('Urkunde_' . $urkunden_daten['vorname'] . '_' . $urkunden_daten['nachname'] . '.pdf', 'I');
?>