<?php
class Saison_14 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2009";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2009";
    }
    public static function platz_to_block($platz, $saison)
    {       
        return Saison::platz_to_block($platz, 13);
    }
    public static function platz_to_wertigkeit($platz, $saison)
    {       
        return Saison::platz_to_wertigkeit($platz, 13);
    }  
}