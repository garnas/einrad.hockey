<?php
class Saison_11 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2006";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2006";
    }
    public static function platz_to_block($platz, $saison)
    {       
        return Saison::platz_to_block($platz, 8);
    }
    public static function platz_to_wertigkeit($platz, $saison)
    {       
        return Saison::platz_to_wertigkeit($platz, 8);
    }  
}