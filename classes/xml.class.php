<?php

class xml
{
    //function defination to convert array to xml
    public static function array_to_xml($array, $xml, $ebene1="node1", $ebene3="node3")
    {
        foreach ($array as $key1 => $value1) {
            if ($ebene1=="meldungen") {
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
                                    if (is_array($value4)) {
                                    } else {
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
                        } elseif ($key2=="string") {
                        } else {
                            $subnode1->addChild("$key2", htmlspecialchars("$value2"));
                        }
                    }
                }
                if ($ebene1=="platz") {
                    $block = Saison::platz_to_block($subnode1->platz, Config::SAISON);
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
}
