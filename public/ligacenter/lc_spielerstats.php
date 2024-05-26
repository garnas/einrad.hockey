<?php
require_once '../../init.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once '../../logic/session_la.logic.php'; //Auth

$sql = "
            SELECT * FROM spieler Inner JOIN teams_liga tl on spieler.team_id = tl.team_id
            inner join teams_details td on tl.team_id = td.team_id
            ";

$result = db::$db->query($sql)->fetch();

$header = [
    [
        "spieler_id",
        "jahrgang",
        "geschlecht",
        "schiri",
        "junior",
        "letzte_saison",
        "platz",
        "rang",
        "block",
        "team_id",
        "teamname",
        "plz",
        "ort",
        "verein"
    ]
];

$auswertung = $header;
foreach ($result as $row) {
    $auswertung[] = [
        $row["spieler_id"],
        $row["jahrgang"],
        $row["geschlecht"],
        $row["schiri"],
        $row["junior"],
        $row["letzte_saison"],
        Tabelle::get_team_meister_platz($row["team_id"]),
        Tabelle::get_team_rang($row["team_id"]),
        Tabelle::get_team_block($row["team_id"]),
        $row["team_id"],
        $row["teamname"],
        $row["plz"],
        $row["ort"],
        $row["verein"],
    ];
}

$spreadsheet = new Spreadsheet();
$spreadsheet
    ->getActiveSheet()
    ->setTitle("Auswertung Spieler")
    ->fromArray($auswertung);

#$spreadsheet->createSheet();
// Zero based, so set the second tab as active sheet
#$spreadsheet->setActiveSheetIndex(1);
#$spreadsheet
#    ->getActiveSheet()
#    ->setTitle("Fragenauswertung")
#    ->fromArray($auswertung_fragen);

$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="spieler.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');