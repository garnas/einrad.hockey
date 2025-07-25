<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Repository\Team\TeamRepository;

require_once '../../init.php';
require_once '../../logic/session_la.logic.php'; //Auth
require_once '../../logic/abstimmung_ergebnis.logic.php';

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';

if (time() < $beginn) {
    Html::message('info', "Die Abstimmung ist zurzeit nicht aktiv. Die Abstimmung startet am " . date("d.m.Y", $beginn) . " und endet am " . date("d.m.Y \u\m H:i", $abschluss) . " Uhr.");
} else if (time() > $beginn && time() < $abschluss) {
    Html::message('info', "Die Abstimmung ist aktiv. Die Abstimmung endet am " . date("d.m.Y \u\m H:i", $abschluss) . " Uhr. Es gaben bisher " . $num_stimmen . " von " . count($teams) . " Teams ihre Stimme ab.");
} else if (time() > $abschluss) {
    Html::message('info', "Die Abstimmung ist beendet. Die Abstimmung endete am " . date("d.m.Y \u\m H:i", $abschluss) . " Uhr. Es gaben bisher " . $num_stimmen . " von " . count($teams) . " Teams ihre Stimme ab.");
} else {
    Html::message('error', "Es konnte kein Zeitraum bestimmt werden.");
}

include '../../templates/abstimmung_ergebnis.tmp.php';
include '../../templates/abstimmung_ergebnis_la.tmp.php';

include '../../templates/footer.tmp.php';