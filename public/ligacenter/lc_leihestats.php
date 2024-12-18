<?php
require_once '../../init.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once '../../logic/session_la.logic.php'; //Auth

$sql = "
    SELECT sl.ausleihe_id, sl.spieler, sl.team_auf, sl.team_ab, tl.saison, tl.spieltag, sl.turnier_id, tl.tblock, tl.datum
    FROM spieler_ausleihen sl
    LEFT JOIN turniere_liga tl ON tl.turnier_id = sl.turnier_id;
";

$result = db::$db->query($sql, )->fetch();

$header = array(
    array(
        "ausleihe_id",
        "spieler",
        "team_auf",
        "team_ab",
        "saison",
        "spieltag",
        "turnier_id",
        "tblock",
        "datum",
    )
);

$auswertung = $header;
foreach ($result as $row) {
    $auswertung[] = array(
        $row["ausleihe_id"],
        $row["spieler"],
        $row["team_auf"],
        $row["team_ab"],
        $row["saison"],
        $row["spieltag"],
        $row["turnier_id"],
        $row["tblock"],
        $row["datum"],
    );
}

$spreadsheet = new Spreadsheet();
$spreadsheet
    ->getActiveSheet()
    ->setTitle("Auswertung Spielerleihen")
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
header('Content-Disposition: attachment;filename="leihe.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');