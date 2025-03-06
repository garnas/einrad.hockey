<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/abstimmung.logic.php';
$teams = TeamRepository::get()->activeLigaTeams();
$stimmen = Abstimmung::get_anzahl_stimmen();

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';

if (time() > strtotime(Abstimmung::ENDE)) {
    include '../../templates/abstimmung_ergebnis.tmp.php';
} else {
    Html::message('notice',
    "Die Abstimmung startet am " . date("d.m.Y", $beginn) . " und endet am "
    . date("d.m.Y \u\m H:i", $abschluss) . " Uhr. "
    . "Es haben bisher " . $stimmen . " von " . count($teams)  . " Teams abgestimmt.",
    Null);
}

include '../../templates/footer.tmp.php';