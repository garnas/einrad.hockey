<?php
class Saison_15 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2010";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2010";
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