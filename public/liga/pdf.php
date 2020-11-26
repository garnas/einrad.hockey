<?php
require_once '../../logic/first.logic.php'; // Autoloader und Session
require_once '../../logic/spielplan.logic.php'; // Erstellt Spielplanobjekt nach Validation

$font_size_array = ['4' => 16, '5' => 18, '6' => 16, '7' => 13];
$font_size = $font_size_array[$spielplan->anzahl_teams];

$white_space = 'table {white-space: normal!important;}';
// Css-Code als String
ob_start();
include "../css/drucken.php"; // Es wurde eine eigene Css-Datei für die PDF-Erstellung erstellt, da der Css-Code nicht immer kompatibel mit mpdf war
$css_style = ob_get_clean();

// Html-Code als String
$penalty_anzeigen = true;
ob_start();
include '../../templates/spielplan/spielplan_vorTurnierTabelle.tmp.php';
include '../../templates/spielplan/spielplan_paarungen.tmp.php';
$html = '<html>' . ob_get_clean() . '</html>';

// PDF-Erstellung
$mpdf = PDF::start_mpdf(); // Erstellt ein MPDF-Objekt aus dem Framework
$mpdf->shrink_tables_to_fit = 4; // Tabellen können um den Faktor 4 verkleinert werden, um noch auf eine Seite zu passen.
$mpdf->SetHTMLHeader('<img src="../bilder/logo_lang_small.png" style="width: 80mm; float: right;">');
$mpdf->WriteHTML($css_style,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html,\Mpdf\HTMLParserMode::HTML_BODY);
$mpdf->Output();