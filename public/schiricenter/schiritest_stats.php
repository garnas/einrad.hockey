<?php
require_once '../../init.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//require_once '../../logic/session_la.logic.php'; //Auth

$sql = "
            SELECT schiri_test_id,
                   spieler.jahrgang,
                   spieler.geschlecht,
                   schiri_ergebnis.test_level,
                   bestanden,
                   t_gestartet,
                   t_abgegeben,
                   gestellte_fragen,
                   gesetzte_antworten,
                   t_erstellt
            FROM schiri_ergebnis
            INNER JOIN spieler ON schiri_ergebnis.spieler_id = spieler.spieler_id
            WHERE t_erstellt > '2020-03-01 00:00:00'
            ";

$result = db::$db->query($sql)->fetch();

$auswertung_fragen = [
    [
        "frage_id",
        "richtig",
        "falsch",
        "antwort_1",
        "antwort_2",
        "antwort_3",
        "antwort_4",
        "Testlevel",
        "t_erstellt",

    ]
];

$auswertung_test = [
    [
        "test_id",
        "bestanden",
        "TestLevel",
        "jahrgang",
        "geschlecht",
        "anzahl_fragen",
        "anzahl_beantwortet",
        "anzahl_richtige_fragen",
        "t_erstellt",
        "t_gestartet",
        "t_abgegeben"
    ]
];

foreach ($result as $test) {
    //  Ignoriere tests ohne antworten
    if (empty($test["gesetzte_antworten"])) {
        continue;
    }

    $fragen = explode(",", $test["gestellte_fragen"]);
    $antworten = json_decode($test["gesetzte_antworten"], true);
    $anzahl_beantwortet = count($antworten);
    $anzahl_richtiger_fragen = 0;

    foreach ($fragen as $i => $frage_id) {
        $antwort = array_keys($antworten[$i]);
        $richtig = SchiriTest::validate_frage($frage_id, $antwort) ? 1 : 0;
        $falsch = SchiriTest::validate_frage($frage_id, $antwort) ? 0 : 1;
        $anzahl_richtiger_fragen += $richtig;

        $auswertung_fragen[] = [
            $frage_id,
            $richtig,
            $falsch,
            (in_array(1, $antwort)) ? 1 : 0,
            (in_array(2, $antwort)) ? 1 : 0,
            (in_array(3, $antwort)) ? 1 : 0,
            (in_array(4, $antwort)) ? 1 : 0,
            $test["test_level"],
            $test["t_erstellt"],
        ];
    }

    $auswertung_test[] = [
        $test["schiri_test_id"],
        $test["bestanden"],
        $test["test_level"],
        $test["jahrgang"],
        $test["geschlecht"],
        count($fragen),
        $anzahl_beantwortet,
        $anzahl_richtiger_fragen,
        $test["t_erstellt"],
        $test["t_gestartet"],
        $test["t_abgegeben"]
    ];
}

$spreadsheet = new Spreadsheet();
$spreadsheet
    ->getActiveSheet()
    ->setTitle("Testauswertung")
    ->fromArray($auswertung_test);

$spreadsheet->createSheet();
// Zero based, so set the second tab as active sheet
$spreadsheet->setActiveSheetIndex(1);
$spreadsheet
    ->getActiveSheet()
    ->setTitle("Fragenauswertung")
    ->fromArray($auswertung_fragen);

$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="fragen_auswertung.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');