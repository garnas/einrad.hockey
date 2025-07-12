<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../init.php';

Helper::$log_user = false; // Keine User-Logs

//Assoziatives Array der Rangtabelle
$rang_tabelle = tabelle::get_rang_tabelle(Tabelle::get_aktuellen_spieltag()-1);

$xml = new SimpleXMLElement('<rangtabelle/>');

header('Content-type: text/xml');
echo xml::array_to_xml($rang_tabelle, $xml, "platz");
