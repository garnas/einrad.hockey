<?php
/////////////////////////////////////////////////////////////////////////////
////////////////////////////////////LOGIK////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////
require_once '../../logic/first.logic.php'; //autoloader und Session

//Assoziatives Array der Rangtabelle
$rang_tabelle = tabelle::get_rang_tabelle(Tabelle::get_aktuellen_spieltag()-1);

$xml = new SimpleXMLElement('<rangtabelle/>');

xml::array_to_xml($rang_tabelle, $xml, "platz");

Header('Content-type: text/xml');
print($xml->asXML());
