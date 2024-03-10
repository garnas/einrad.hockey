<?php
require_once '../../init.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once '../../logic/session_la.logic.php'; //Auth

$sql = "
            SELECT * FROM turniere_liga Inner JOIN turniere_details t on turniere_liga.turnier_id = t.turnier_id
                Inner JOIN teams_liga tl on turniere_liga.ausrichter = tl.team_id
            ";
$result = db::$db->query($sql)->fetch();

$header = [
    [
        "turnier_id",
        "art",
        "ort",
        "tblock",
        "datum",
        "sofort_oeffnen",
        "saison",
        "canceled",
        "canceled_grund",
        "phase",
        "anzahl_teams",
        "team_id",
        "teamname",
        "plaetze"
    ]
];

$auswertung = $header;
foreach ($result as $row) {
    $auswertung[] = [
        $row["turnier_id"],
        $row["art"],
        $row["ort"],
        $row["tblock"],
        $row["datum"],
        $row["sofort_oeffnen"],
        $row["saison"],
        $row["canceled"],
        $row["canceled_grund"],
        $row["phase"],
        \App\Service\Turnier\TurnierService::getAnzahlAngemeldeteTeams(\App\Repository\Turnier\TurnierRepository::get()->turnier($row["turnier_id"])),
        $row["team_id"],
        $row["teamname"],
        $row["plaetze"]
    ];
}

$spreadsheet = new Spreadsheet();
$spreadsheet
    ->getActiveSheet()
    ->setTitle("Auswertung Turnier")
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
header('Content-Disposition: attachment;filename="turniere.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');