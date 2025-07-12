<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

Helper::$log_user = false; // Keine User-Logs

// Assoziatives Array aller Turnieranmeldungen der Aktuellen Saison
$turnieranmeldungen = nTurnier::get_all_anmeldungen();

$xml = new SimpleXMLElement('<turnieranmeldungen/>');
$xml_content = xml::array_to_xml($turnieranmeldungen,$xml,"meldungen","team");

header('Content-type: text/xml');
echo $xml_content;
