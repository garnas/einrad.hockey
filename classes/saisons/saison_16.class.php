<?php
class Saison_16 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2011";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2011";
    }

    public static function platz_to_block($platz, $saison)
    {       
        if (empty($platz)){
            return '';
        }
        //Blockzuordnung
        $blocks=array(
            "A"=>range(1,8),
            "AB"=>range(9,16),
            "BC"=>range(17,24),
            "CD"=>range(25,32),
            "DE"=>range(33,40),
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
            return 150;
        }
        //Sprung AB->A
        if ($platz ==9)
        return Saison_16::platz_to_wertigkeit($platz-1, $saison)-18;
        //Sprung BC->AB
        if ($platz ==17)
            return Saison_16::platz_to_wertigkeit($platz-1, $saison)-14;
        //Sprung CD->BC
        if ($platz ==25)
            return Saison_16::platz_to_wertigkeit($platz-1, $saison)-10;
        //Sprung DE->CD
        if ($platz ==33)
            return Saison_16::platz_to_wertigkeit($platz-1, $saison)-6;

        return max(Saison_16::platz_to_wertigkeit($platz-1, $saison)-2, 6);
    }
}