<?php
class Saison_20 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2015";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2015";
    }
    public static function platz_to_block($platz, $saison)
    {       
        if (empty($platz)){
            return '';
        }
        //Blockzuordnung
        $blocks=array(
            "A"=>range(1,6),
            "AB"=>range(7,12),
            "BC"=>range(13,18),
            "CD"=>range(19,24),
            "DE"=>range(25,30),
            "EF"=>range(31,36),
            //"F"=>range(1,999),
            );
        foreach ($blocks as $block => $platzierung){
            if (in_array($platz, $platzierung)){
                return $block;
            }
        }
        return "F";
    }

    //Weist dem Platz in der Rangtabelle eine Wertigkeit zu
    public static function platz_to_wertigkeit($platz, $saison = Config::SAISON)
    {
        if (empty($platz)){
            return '';
        }
        if ($platz ==1){
            return 200;
        }
        if (in_array($platz, range(1,13))){
            return Saison_20::platz_to_wertigkeit($platz-1, $saison)-8;
        }
        if (in_array($platz, range(14,25))){
            return Saison_20::platz_to_wertigkeit($platz-1, $saison)-4;
        }
        if (in_array($platz, range(16,37))){
            return Saison_20::platz_to_wertigkeit($platz-1, $saison)-2;
        }
        return max(Saison_20::platz_to_wertigkeit($platz-1, $saison)-1, 20);
    }
}