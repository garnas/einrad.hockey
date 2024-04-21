<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';
require_once '../../logic/session_oa.logic.php'; //Auth

//Formularauswertung
require_once '../../logic/neuigkeit_bearbeiten.logic.php';

Html::notice("Die Verwendung von Html-Tags ist als Öffentlichkeitsausschuss standardmäßig aktiviert.");

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LAYOUT///////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
include '../../templates/header.tmp.php';
include '../../templates/neuigkeit_bearbeiten.tmp.php';
include '../../templates/footer.tmp.php';