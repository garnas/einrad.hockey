<?php
require_once '../../init.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

//require_once '../../logic/session_la.logic.php'; //Auth

$sql = "
            SELECT schiri_ergebnis.test_level, gestellte_fragen, gesetzte_antworten,
                   t_erstellt, bestanden
            FROM schiri_ergebnis
            INNER JOIN spieler ON schiri_ergebnis.spieler_id = spieler.spieler_id
            WHERE t_erstellt > '2020-03-01 00:00:00'
            ";
$result = db::$db->query($sql)->fetch();
//db::debug($result);

//db::debug($result["gesetzte_antworten"]);

//db::debug(SchiriTest::validate_frage($fragen[0], array_keys($test[0])), true);

$auswertung = [
    [
        "frage_id",
        "richtig",
        "falsch",
        "antwort_1",
        "antwort_2",
        "antwort_3",
        "antwort_4",
        "Testlevel",
        "Erstellungszeitpunkt"
    ]
];


foreach ($result as $test) {
    $fragen = explode(",", $test["gestellte_fragen"]);
    if (empty($test["gesetzte_antworten"])) {
        continue;
    }
    $antworten = json_decode($test["gesetzte_antworten"], true);
    $erstellungszeitpunkt = $test["t_erstellt"];
    $test_level = $test["test_level"];

    foreach ($fragen as $i => $frage_id) {

        $antwort = array_keys($antworten[$i]);
        db::debug($antwort);
        $richtig = SchiriTest::validate_frage($frage_id, $antwort) ? 1 : 0;
        $falsch = SchiriTest::validate_frage($frage_id, $antwort) ? 0 : 1;

        $auswertung[] = [$frage_id,
            $richtig,
            $falsch,
            (in_array(1, $antwort)) ? 1 : 0,
            (in_array(2, $antwort)) ? 1 : 0,
            (in_array(3, $antwort)) ? 1 : 0,
            (in_array(4, $antwort)) ? 1 : 0,
            $test_level,
            $erstellungszeitpunkt,
        ];

    }
}

$spreadsheet = new Spreadsheet();
$activeWorksheet = $spreadsheet->getActiveSheet();

$activeWorksheet->fromArray($auswertung);

$sql = "
            SELECT schiri_ergebnis.spieler_id, schiri_ergebnis.test_level, gestellte_fragen, gesetzte_antworten,
                   t_erstellt, bestanden
            FROM schiri_ergebnis
            INNER JOIN spieler ON schiri_ergebnis.spieler_id = spieler.spieler_id
            WHERE t_erstellt > '2020-03-01 00:00:00'
            ";
$result = db::$db->query($sql)->fetch();



include '../../templates/header.tmp.php';