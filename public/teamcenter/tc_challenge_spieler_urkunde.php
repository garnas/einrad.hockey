<?php
require_once '../../logic/first.logic.php'; // Autoloader und Session
require_once '../../logic/session_team.logic.php'; //Auth
require_once '../../logic/challenge.logic.php'; // Logic der Challenge

$spieler_id = $_GET['spieler_id'];
$urkunden_daten = $challenge->get_spieler_result($spieler_id);

if ($urkunden_daten['geschlecht'] == "m") {
    $anrede = "Der Spieler";
} else {
    $anrede = "Die Spielerin";
}

// Css-Code als String
ob_start();
// Es wurde eine eigene Css-Datei für die PDF-Erstellung erstellt, da der Css-Code nicht immer kompatibel mit mpdf war
include "../css/pdf.css.php";

$css_style = ob_get_clean();

// Start der PDF Erstellung
$mpdf = PDF::start_mpdf();

$html = '
<div style="height: 50mm;">
    <div style="float: right; width: 45mm;"><img src="../bilder/logo_kurz_small.png"></div>
</div>
<div style="font-size: 16px;">
    <div style="margin-bottom: 5mm;">' . $anrede . '</div>
    <div class="w3-text-primary" style="font-size: 28px; font-weight: bold;">' . $urkunden_daten['vorname'] . ' ' . $urkunden_daten['nachname'] . '</div>
    <div class="w3-text-primary" style="margin-bottom: 10mm; font-weight: bold;">' . $urkunden_daten['teamname'] . '</div>
    <div style="">erreichte bei der</div>
    <div style="margin-bottom: 10mm; font-size: 28px;">km-Challenge 2020</div>
    <div style="">mit</div>
    <div style="margin-bottom: 10mm; font-size: 28px;">' . number_format($urkunden_daten['kilometer'], 1, ',', '.') . ' km' . '</div>
    <div style="">den</div>
    <div style="font-size: 28px;">' . $urkunden_daten['platz'] . '. Platz' . '</div>
</div>
';

// PDF Bearbeitung
$mpdf->SetTitle('Urkunde km-Challenge 2020');
// $mpdf->SetHTMLHeader('<img src="../bilder/logo_kurz_small.png" style="width: 15mm; float: right;">');
// $mpdf->SetHTMLFooter('<p>Das ist ein Text</p>');
$mpdf->WriteHTML($css_style,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html,\Mpdf\HTMLParserMode::HTML_BODY);

// Output - Option 'D' für Download, 'I' für im Browser anzeigen
$mpdf->Output('Urkunde.pdf', 'I');
?>