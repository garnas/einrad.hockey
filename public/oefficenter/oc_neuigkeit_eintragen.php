<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_oa.logic.php'; //auth
require_once '../../logic/neuigkeit_eintragen.logic.php'; //Formularauswertung

Html::notice("Die Verwendung von Html-Tags ist als Öffentlichkeitsausschuss standardmäßig aktiviert.");

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/neuigkeit_eintragen.tmp.php';
include '../../templates/footer.tmp.php';