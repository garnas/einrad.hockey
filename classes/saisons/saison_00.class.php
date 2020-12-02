<?php
class Saison_00 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "01.02.1995";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.1995";
    }
    public static function platz_to_block($platz, $saison)
    {       
        return "";
    }
    public static function platz_to_wertigkeit($platz, $saison = Config::SAISON)
    {
        return 0;
    }
}