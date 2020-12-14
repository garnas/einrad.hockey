<?php
class Saison_08 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2003";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2003";
    }
    public static function platz_to_block($platz, $saison)
    {       
        return "";
    }
    //Weist dem Platz in der Rangtabelle eine Wertigkeit zu
    public static function platz_to_wertigkeit($platz, $saison = Config::SAISON)
    {
        if (empty($platz)){
            return '';
        }
        if ($platz ==1){
            return 132;
        }

        return max(Saison_08::platz_to_wertigkeit($platz-1, $saison)-2, 0);
    }
}