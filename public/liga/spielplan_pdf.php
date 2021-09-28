<?php
require_once '../../init.php'; // Autoloader und Session
require_once '../../logic/spielplan.logic.php'; // Erstellt Spielplanobjekt nach Validation

// Legt die Schriftgrößen fest je nach Teamanzahl
$font_size_array = ['4' => 15, '5' => 15, '6' => 14, '7' => 14]; // in px
$font_size = $font_size_array[$spielplan->anzahl_teams] ?? '12';

// Legt das Padding der table <td>s fest je nach Teamanzahl
$padding_array = ['4' => 11, '5' => 11, '6' => 8, '7' => 4];  // in px
$padding = $padding_array[$spielplan->anzahl_teams] ?? 3;

// Css-Code als String
ob_start();
include "../css/pdf.css.php"; // Es wurde eine eigene Css-Datei für die PDF-Erstellung erstellt, da der Css-Code nicht immer kompatibel mit mpdf war
$css_style = ob_get_clean();

// Html-Code als String
$penalty_anzeigen = true;
ob_start();
include '../../templates/spielplan/spielplan_titel.tmp.php';
include '../../templates/spielplan/spielplan_teamliste.tmp.php';
include '../../templates/spielplan/spielplan_spiele.tmp.php';

$html = 
    '<html>
        <head>
            <meta charset="UTF-8">
            <link rel="shortcut icon" href="../bilder/favicon/favicon.png">
        </head>' 
        .ob_get_clean() 
    .'</html>';
$html = str_replace('<br>','', $html);

// PDF-Erstellung
$mpdf = MPDF::load_mpdf(); // Erstellt ein MPDF-Objekt aus dem Framework
$mpdf->shrink_tables_to_fit = 4; // Tabellen können um den Faktor 4 verkleinert werden, um noch auf eine Seite zu passen.

// PDF beschreiben
$mpdf->SetTitle('Spielplan ' . $spielplan->turnier->get_ort());
$mpdf->SetHTMLHeader('<img src="../bilder/logo_lang_small.png" style="margin-top:18px; width: 70mm; float: right;">');
$mpdf->SetHTMLFooter(
    '<table style="width: 100%">
        <tr>
            <td>' . Html::link('https://www.einrad.hockey/liga/spielplan?turnier_id=' . $turnier_id,'www.einrad.hockey').'</td>
            <td class="w3-right-align">'.date("d.m.Y").'</td>
        </tr>
    </table>'
);
$mpdf->WriteHTML($css_style,\Mpdf\HTMLParserMode::HEADER_CSS);
$mpdf->WriteHTML($html,\Mpdf\HTMLParserMode::HTML_BODY);

// Output - Otpion 'D' für Download, 'I' für im Browser anzeigen
$mpdf->Output('Spielplan '. $spielplan->turnier->get_ort() . '.pdf', 'I');
