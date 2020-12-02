<?php
class Saison_26 {
    //SAISON_ANFANG
    public static function get_saison_anfang()
    {
        return "15.08.2020";
    }
    //SAISON_ENDE
    public static function get_saison_ende()
    {
        return "31.10.2021";
    }
    //Ligagebühr
    public static function get_ligagebuehr()
    {
        return "30&nbsp;€";
    }

    ///////////////////////////////////////////////////////////////
    ///////////////////////////Teamblöcke//////////////////////////
    ///////////////////////////////////////////////////////////////
    //Für die Block und Wertzuordnung in der Rangtabelle siehe Saison::platz_to_block und Saison::platz_to_wertigkeit
    //Reihenfolge bei den Blöcken muss immer hoch -> niedrig sein

    //Mögliche Team-Blöcke
    public static function get_block()
    {
        return array('A','AB','BC','CD','DE','EF','F');
    }
    //Mögliche Turnier-Blöcke 
    public static function get_block_all()
    {
        return array("ABCDEF",'A','AB','ABC','BC','BCD','CD','CDE','DE','DEF','EF','F');
    }
    public static function platz_to_block($platz, $saison)
    {       
        return Saison::platz_to_block($platz, 26);
    }
    public static function platz_to_wertigkeit($platz, $saison)
    {       
        return Saison::platz_to_wertigkeit($platz, 26);
    }    
}