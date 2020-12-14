<?php
class Saison_25 {
    //Abgebrochene Corona Saison
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.2020";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "17.06.2020";
    }
    public static function platz_to_block($platz, $saison)
    {       
        return Saison::platz_to_block($platz, 22);
    }
    public static function platz_to_wertigkeit($platz, $saison)
    {       
        return Saison::platz_to_wertigkeit($platz, 22);
    }   
}