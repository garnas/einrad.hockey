<?php
class Saison_18 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2013";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2013";
    }
    public static function platz_to_block($platz, $saison)
    {       
        return Saison::platz_to_block($platz, 16);
    }
    public static function platz_to_wertigkeit($platz, $saison)
    {       
        return Saison::platz_to_wertigkeit($platz, 16);
    }  
}