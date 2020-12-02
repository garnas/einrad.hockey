<?php
class Saison_12 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2007";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2007";
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