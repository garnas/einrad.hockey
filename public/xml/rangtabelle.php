<?php

/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
use App\Service\Turnier\TabelleService;

require_once '../../init.php';

Helper::$log_user = false; // Keine User-Logs

//Assoziatives Array der Rangtabelle
$rang_tabelle = TabelleService::getRangTabelle(TabelleService::getAktuellenSpieltag() - 1);

$xml = new SimpleXMLElement('<rangtabelle/>');

header('Content-type: text/xml');
echo xml::array_to_xml($rang_tabelle, $xml, "platz");
