<?php

class xml {
    //function defination to convert array to xml
      public static function array_to_xml($array, &$xml) {
        foreach($array as $key => $value) {
            if(is_array($value)) {
                if(!is_numeric($key)){
                    $subnode = $xml->addChild("$key");
                    xml::array_to_xml($value, $subnode);
                }else{
                    $subnode = $xml->addChild("item$key");
                    xml::array_to_xml($value, $subnode);
                }
            }else {
                $xml->addChild("$key",htmlspecialchars("$value"));
            }
        }
    }
  }
