<?php

use App\Turnier\Entity\Turnier;
use App\Turnier\Entity\Turnieranmeldung;
use App\Tabelle\Service\TabelleService;

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
                    $block = TabelleService::rangToBlock((int) $subnode1->rang);
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
            $xml_child->addChild('turnier_id', e($turnier->id()));
            $xml_child->addChild('tname', e($turnier->getName()));
            $xml_child->addChild('ausrichter', e($turnier->getAusrichter()->id()));
            $xml_child->addChild('tblock', e($turnier->getBlock()));
            $xml_child->addChild('datum', e($turnier->getDatum()->format('Y-m-d')));
            $xml_child->addChild('spieltag', e($turnier->getSpieltag()));
            $xml_child->addChild('phase', e($turnier->getPhase()));
            $xml_child->addChild('spielplan_vorlage', e($turnier->getSpielplanVorlage()?->getSpielplan()));
            $xml_child->addChild('spielplan_datei', e($turnier->getSpielplanDatei()));
            $xml_child->addChild('saison', e($turnier->getSaison()));
            $xml_child->addChild('hallenname', e($turnier->getDetails()->getHallenname()));
            $xml_child->addChild('strasse', e($turnier->getDetails()->getStrasse()));
            $xml_child->addChild('plz', e($turnier->getDetails()->getPlz()));
            $xml_child->addChild('ort', e($turnier->getDetails()->getOrt()));
            $xml_child->addChild('haltestellen', e($turnier->getDetails()->getHaltestellen()));
            $xml_child->addChild('plaetze', e($turnier->getDetails()->getPlaetze()));
            $xml_child->addChild('format', e($turnier->getDetails()->getFormat()));
            $xml_child->addChild('startzeit', e($turnier->getDetails()->getStartzeit()->format('H:i')));
            $xml_child->addChild('besprechung', e($turnier->getDetails()->getBesprechung()));
            $xml_child->addChild('hinweis', e($turnier->getDetails()->getHinweis()));
            $xml_child->addChild('organisator', e($turnier->getDetails()->getOrganisator()));
            $xml_child->addChild('handy', e($turnier->getDetails()->getHandy()));
            $xml_child->addChild('startgebuehr', e($turnier->getDetails()->getStartgebuehr()));
            $xml_child->addChild('teamname', e($turnier->getAusrichter()->getName()));
        }
        return $xml->asXML();
    }

    /**
     * @param Turnieranmeldung[] $anmeldungen
     * @return string|bool
     */
    public static function turnieranmeldungenToXml(array $anmeldungen): string|bool
    {
        // Erhalten den aktuellen Spieltag für die Ermittung des Teamblocks und der -wertigkeit
        $spieltag = TabelleService::getAktuellenSpieltag();

        // Erstellung des Arrays mit der TurnierID, der -liste, Teamnamen, -blöcken und -wertigkeiten
        $turnier_listen = [];
        foreach ($anmeldungen as $anmeldung) {
            $turnier_id = $anmeldung->getTurnier()->id();
            $team_id = $anmeldung->getTeam()->id();
            $turnier_listen[$turnier_id][$anmeldung->getListe()][$team_id] = db::escape([
                'liste_id' => $anmeldung->getListeId(),
                'turnier_id' => $turnier_id,
                'team_id' => $team_id,
                'liste' => $anmeldung->getListe(),
                'position_warteliste' => $anmeldung->getPositionWarteliste(),
                'freilos_gesetzt' => $anmeldung->getFreilosGesetzt(),
                'freilos_gesetzt_am' => $anmeldung->getFreilosGesetztAm()?->format('Y-m-d H:i:s'),
                'teamname' => $anmeldung->getTeam()->getName(),
                'ligateam' => $anmeldung->getTeam()->getLigateam(),
                'tblock' => TabelleService::getTeamBlock($team_id, $spieltag),
                'wertigkeit' => TabelleService::getTeamWertigkeit($team_id, $spieltag),
            ]);
        }

        $xml = new SimpleXMLElement('<turnieranmeldungen/>');
        return self::array_to_xml($turnier_listen, $xml, "meldungen", "team");
    }
}
