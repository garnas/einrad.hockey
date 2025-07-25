<?php

use App\Entity\Turnier\Turnier;

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
    public static function array_to_xml($array, SimpleXMLElement $xml, $ebene1 = "node1", $ebene3 = "node3"): bool|string
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
        return $xml->asXML();
    }

    /**
     * Function definition to convert array to xml
     *
     * @param Turnier[] $turniere
     */
    public static function turniereToXml(array $turniere): string|bool
    {
        $xml = new SimpleXMLElement('<turniere/>');
        foreach ($turniere as $turnier) {
            $xml_child = $xml->addChild('turnier');
            $xml_child->addChild('turnier_id', $turnier->id());
            $xml_child->addChild('tname', $turnier->getName());
            $xml_child->addChild('ausrichter', $turnier->getAusrichter()->id());
            $xml_child->addChild('tblock', $turnier->getBlock());
            $xml_child->addChild('datum', $turnier->getDatum()->format('Y-m-d'));
            $xml_child->addChild('spieltag', $turnier->getSpieltag());
            $xml_child->addChild('phase', $turnier->getPhase());
            $xml_child->addChild('spielplan_vorlage', $turnier->getSpielplanVorlage()->getSpielplan());
            $xml_child->addChild('spielplan_datei', $turnier->getSpielplanDatei());
            $xml_child->addChild('saison', $turnier->getSaison());
            $xml_child->addChild('hallenname', $turnier->getDetails()->getHallenname());
            $xml_child->addChild('strasse', $turnier->getDetails()->getStrasse());
            $xml_child->addChild('plz', $turnier->getDetails()->getPlz());
            $xml_child->addChild('ort', $turnier->getDetails()->getOrt());
            $xml_child->addChild('haltestellen', $turnier->getDetails()->getHaltestellen());
            $xml_child->addChild('plaetze', $turnier->getDetails()->getPlaetze());
            $xml_child->addChild('format', $turnier->getDetails()->getFormat());
            $xml_child->addChild('startzeit', $turnier->getDetails()->getStartzeit()->format('H:i'));
            $xml_child->addChild('besprechung', $turnier->getDetails()->getBesprechung());
            $xml_child->addChild('hinweis', $turnier->getDetails()->getHinweis());
            $xml_child->addChild('organisator', $turnier->getDetails()->getOrganisator());
            $xml_child->addChild('handy', $turnier->getDetails()->getHandy());
            $xml_child->addChild('startgebuehr', $turnier->getDetails()->getStartgebuehr());
            $xml_child->addChild('teamname', $turnier->getAusrichter()->getName());
        }
        return $xml->asXML();
    }
}
