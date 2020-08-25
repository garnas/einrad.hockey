<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session
require_once '../../logic/session_la.logic.php';//Auth

$turnier_id = 914;
$spielplan = new Spielplan($turnier_id);
$spielplan->create_spielplan_jgj();
//$spielplan->update_spiel(1, 3,4,NULL,NULL);
$tabelle=$spielplan->get_turnier_tabelle();
db::debug($tabelle);
