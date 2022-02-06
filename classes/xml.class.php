<?php

/**
 * Class xml
 *
 * Kreiert die XML-Schnittstelle der Liga
 *
 */
class xml
{
    /**
     * Function definition to convert array to xml
     *
     * @param $array
     * @param SimpleXMLElement $xml
     * @param string $ebene1
     * @param string $ebene3
     */
    public static function array_to_xml($array, SimpleXMLElement $xml, $ebene1 = "node1", $ebene3 = "node3")
    {
        foreach ($array as $key1 => $value1) {
            if ($ebene1 == "meldungen") {
                $subnode1 = $xml->addChild("turnier_id_$key1");
            } else {
                $subnode1 = $xml->addChild("$ebene1");
            }

            if (is_array($value1)) {
                foreach ($value1 as $key2 => $value2) {
                    if (is_array($value2)) {
                        $subnode2 = $subnode1->addChild("$key2");
                        foreach ($value2 as $key3 => $value3) {
                            if (is_array($value3)) {
                                $subnode3 = $subnode2->addChild("$ebene3");
                                foreach ($value3 as $key4 => $value4) {
                                    if (!is_array($value4)) {
                                        if (is_numeric($key4)) {
                                            $subnode3->addChild("_$key4", htmlspecialchars("$value4"));
                                        } else {
                                            $subnode3->addChild("$key4", htmlspecialchars("$value4"));
                                        }
                                    }
                                }
                            } else {
                                if (is_numeric($key3)) {
                                    $subnode2->addChild("_$key3", htmlspecialchars("$value3"));
                                } else {
                                    $subnode2->addChild("$key3", htmlspecialchars("$value3"));
                                }
                            }
                        }
                    } else {
                        if (is_numeric($key2)) {
                            $subnode1->addChild("_$key2", htmlspecialchars("$value2"));
                        } elseif ($key2 !== "string") {
                            $subnode1->addChild("$key2", htmlspecialchars("$value2"));
                        }
                    }
                }
                if ($ebene1 == "platz") {
                    $block = Tabelle::rang_to_block((int)$subnode1->rang);
                    $subnode1->addChild("block", htmlspecialchars("$block"));
                }
            } else {
                if (is_numeric($key1)) {
                    $xml->addChild("_$key1", htmlspecialchars("$value1"));
                } else {
                    $xml->addChild("$key1", htmlspecialchars("$value1"));
                }
            }
        }
        Header('Content-type: text/xml');
        print($xml->asXML());
    }

    /**
     * Function definition to convert array to xml
     *
     * @param $array
     * @param SimpleXMLElement $xml
     * @param string $ebene1
     * @param string $ebene3
     */
    public static function turnier_array_to_xml($array, SimpleXMLElement $xml)
    {
        foreach ($array as $turnier) {
            $xml_child = $xml->addChild('turnier');

            $xml_child->addChild('turnier_id', $turnier->get_turnier_id());
            $xml_child->addChild('tname', $turnier->get_tname());
            $xml_child->addChild('ausrichter', $turnier->get_ausrichter());
            $xml_child->addChild('tblock', $turnier->get_tblock());
            $xml_child->addChild('datum', $turnier->get_datum());
            $xml_child->addChild('spieltag', $turnier->get_spieltag());
            $xml_child->addChild('phase', $turnier->get_phase());
            $xml_child->addChild('spielplan_vorlage', $turnier->get_spielplan_vorlage());
            $xml_child->addChild('spielplan_datei', $turnier->get_spielplan_datei());
            $xml_child->addChild('saison', $turnier->get_saison());
            $xml_child->addChild('hallenname', $turnier->get_hallenname());
            $xml_child->addChild('strasse', $turnier->get_strasse());
            $xml_child->addChild('plz', $turnier->get_plz());
            $xml_child->addChild('ort', $turnier->get_ort());
            $xml_child->addChild('haltestellen', $turnier->get_haltestellen());
            $xml_child->addChild('plaetze', $turnier->get_plaetze());
            $xml_child->addChild('format', $turnier->get_format());
            $xml_child->addChild('startzeit', $turnier->get_startzeit());
            $xml_child->addChild('besprechung', $turnier->get_besprechung());
            $xml_child->addChild('hinweis', $turnier->get_hinweis());
            $xml_child->addChild('organisator', $turnier->get_organisator());
            $xml_child->addChild('handy', $turnier->get_handy());
            $xml_child->addChild('startgebuehr', $turnier->get_startgebuehr());
            $xml_child->addChild('teamname', Team::id_to_name($turnier->get_ausrichter()));
        }

        Header('Content-type: text/xml');
        print($xml->asXML());
    }

}
