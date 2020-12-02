<?php
class Saison_06 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2001";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2001";
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
            return 150;
        }

        return max(Saison_06::platz_to_wertigkeit($platz-1, $saison)-4, 0);
    }
}