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
// ob_start();
// Es wurde eine eigene Css-Datei für die PDF-Erstellung erstellt, da der Css-Code nicht immer kompatibel mit mpdf war
include "../css/pdf.css.php";
//$css_style = ob_get_clean();

$css_style = '
.standard {
    position: absolute;
    font-size: 20px;
    font-family: Verdana;
}
';

// Start der PDF Erstellung
$mpdf = PDF::start_mpdf();

$html = '
<div class="standard" style="top: 25mm; left: 25mm; width: 5mm; height: 250mm; background-color: #6b7ebd;"></div>
<div class="standard" style="top: 55mm; left: 35mm;";>
    <div style="">' . $anrede . '</div>
    <div style="padding-bottom: 15mm;">
        <div style="font-size: 36px;">' . $urkunden_daten['vorname'] . ' ' . $urkunden_daten['nachname'] . '</div>
        <div style="">' . $urkunden_daten['teamname'] . '</div>
    </div>
    <div style="">erreichte bei der</div>
    <div style="padding-bottom: 15mm;">
        <div style="font-size: 36px;">km-Challenge 2020</div>
        <div style="">13.11.2020 - 20.12.2020</div>
    </div>
    <div style="">mit</div>
    <div style="padding-bottom: 15mm; font-size: 36px;">' . number_format($urkunden_daten['kilometer'], 1, ',', '.') . ' km' . '</div>
    <div style="">den</div>
    <div style="padding-bottom: 15mm; font-size: 36px;">' . $urkunden_daten['platz'] . '. Platz' . '</div>
</div>
<div class="standard" style="bottom: 22mm; right: 25mm; width: 40mm;"><img src="../bilder/logo_kurz_small.png"></div>
<div class="standard" style="bottom: 22mm; left: 35mm;"><span style="font-size: 16px;">21.12.2020</span></div>
';

// PDF Bearbeitung
$mpdf->SetTitle('Urkunde km-Challenge 2020');
$mpdf->WriteHTML($css_style,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html,\Mpdf\HTMLParserMode::HTML_BODY);

// Output - Option 'D' für Download, 'I' für im Browser anzeigen
$mpdf->Output('Urkunde.pdf', 'I');
?>