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

$anzahl_spieler = static function ($team_id) {
    $saison = Config::SAISON;
    $query = "SELECT COUNT(*) FROM spieler WHERE team_id = $team_id AND letzte_saison = $saison";
    return db::$db->query($query)->fetch_one();
};

$anzahl_turniere = static function ($team_id) {
    $saison = Config::SAISON;
    $query = "SELECT COUNT(*) FROM turniere_liste liste
                INNER JOIN turniere_liga liga ON liste.turnier_id = liga.turnier_id
                WHERE liga.saison = $saison
                AND liste.liste = 'setzliste'
                AND liste.team_id = $team_id"
    ;
    return db::$db->query($query)->fetch_one();
};
$header = [
    [
        "team_id",
        "teamname",
        "anzahl spieler",
        "block",
        "anzahl_gespielter_turniere_aktuelle_saison",
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
    $team_id = $row["team_id"];
    $auswertung[] = [
        $team_id,
        $row["teamname"],
        $anzahl_spieler($team_id),
        Tabelle::get_team_block($row["team_id"]),
        $anzahl_turniere($team_id)
    ];
}

$spreadsheet = new Spreadsheet();
$spreadsheet
    ->getActiveSheet()
    ->setTitle("Auswertung Teams")
    ->fromArray($auswertung);

$writer = new Xlsx($spreadsheet);

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="teams.xlsx"');
header('Cache-Control: max-age=0');

$writer->save('php://output');