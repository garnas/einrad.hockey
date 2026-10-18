<?php

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

use App\Repository\Turnier\TurnierRepository;

Helper::$log_user = false; // Keine User-Logs

// Alle Turnieranmeldungen der Aktuellen Saison
$turnieranmeldungen = TurnierRepository::get()->getAnmeldungenOffeneTurniere();

$xml_content = xml::turnieranmeldungenToXml($turnieranmeldungen);

header('Content-type: text/xml');
echo $xml_content;
