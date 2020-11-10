<?php
class Saison_26 {
    public static function platz_to_block($platz, $saison)
    {       
        return Saison::platz_to_block($platz, $saison-1);
    }
    public static function platz_to_wertigkeit($platz, $saison)
    {       
        return Saison::platz_to_wertigkeit($platz, $saison-1);
    }    
}