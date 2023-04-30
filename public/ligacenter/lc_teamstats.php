<?php
require_once '../../init.php';

use App\Entity\Team\Spieler;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

require_once '../../logic/session_la.logic.php'; //Auth

$sql = "
            SELECT * FROM teams_liga inner join teams_details td on teams_liga.team_id = td.team_id
            WHERE aktiv = 'Ja' 
            ";
$result = db::$db->query($sql)->fetch();

$header = [
    [
        "team_id",
        "teamname",
        "freilose",
        "zweites_freilos",
        "anzahl spieler",
        "block",
        "anzahl turniere",
    ]
];

$filter = static function(Spieler $spieler) {
    return $spieler->getLetzteSaison() < Config::SAISON;
};
$filter2 = static function(\App\Entity\Turnier\TurniereListe $liste) {
    return $liste->getTurnier()->getSaison() != Config::SAISON;
};
$auswertung = $header;
foreach ($result as $row) {
    $auswertung[] = [
        $row["team_id"],
        $row["teamname"],
        $row["freilose"],
        $row["zweites_freilos"],
        \App\Repository\Team\TeamRepository::get()->team($row['team_id'])->getKader()->filter($filter)->count(),
        Tabelle::get_team_block($row["team_id"]),
        \App\Repository\Team\TeamRepository::get()->team($row['team_id'])->getTurniereListe()->filter($filter2)->count(),
    ];
}

$spreadsheet = new Spreadsheet();
$spreadsheet
    ->getActiveSheet()
    ->setTitle("Auswertung Teams")
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
header('Content-Disposition: attachment;filename="teams.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');