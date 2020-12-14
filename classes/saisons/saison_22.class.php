<?php
class Saison_22 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2017";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2017";
    }
    public static function platz_to_block($platz, $saison)
    {       
        if (empty($platz)){
            return '';
        }
        //Blockzuordnung
        $blocks=array(
            "A"=>range(1,6),
            "AB"=>range(7,14),
            "BC"=>range(14,21),
            "CD"=>range(21,31),
            "DE"=>range(31,43),
            "EF"=>range(43,57),
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
        //Blockzuordnung
        $platzierung = range(1,43);
            //0.97 =>range(44, 999)
        $platz = (float)$platz;
        if (in_array($platz, $platzierung)){
            return round(250*0.955**($platz-1),0);
        }
        return max(array(round(250*0.955**(43)*0.97**($platz-1-43),0),15));
    }
}