<?php
class Saison_09 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2004";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2004";
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
            //"D"=>range(1,999),
            );
        foreach ($blocks as $block => $platzierung){
            if (in_array($platz, $platzierung)){
                return $block;
            }
        }
        return "D";
    }
    public static function platz_to_wertigkeit($platz, $saison)
    {       
        return Saison::platz_to_wertigkeit($platz, 8);
    }  
}