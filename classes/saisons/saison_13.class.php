<?php
class Saison_13 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2008";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2008";
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
            //"E"=>range(1,999),
            );
        foreach ($blocks as $block => $platzierung){
            if (in_array($platz, $platzierung)){
                return $block;
            }
        }
        return "E";
    }

    //Weist dem Platz in der Rangtabelle eine Wertigkeit zu
    public static function platz_to_wertigkeit($platz, $saison = Config::SAISON)
    {
        if (empty($platz)){
            return '';
        }
        if ($platz ==1){
            return 134;
        }
        //Sprung AB->A
        if ($platz ==7)
        return Saison_13::platz_to_wertigkeit($platz-1, $saison)-10;
        //Sprung BC->AB
        if ($platz ==13)
            return Saison_13::platz_to_wertigkeit($platz-1, $saison)-8;
        //Sprung CD->BC
        if ($platz ==19)
            return Saison_13::platz_to_wertigkeit($platz-1, $saison)-6;
        //Sprung DE->CD
        if ($platz ==25)
            return Saison_13::platz_to_wertigkeit($platz-1, $saison)-4;

        return max(Saison_13::platz_to_wertigkeit($platz-1, $saison)-2, 0);
    }
}