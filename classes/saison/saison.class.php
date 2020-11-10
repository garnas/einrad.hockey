<?php
class Saison {
    public static function get_saison_class($saison)
    {
        $class_map = array(
            '26'=>NEW Saison_26(),
            '25'=>NEW Saison_25(),
        );
        return $class_map[$saison];
    }
    //Funktion für Saisonumstellung auf eine Saison über zwei Jahre
    //0 = Saison 1995
    public static function get_saison_string($saison = Config::SAISON)
    {
        //Sollte zum Beispiel ein String übergeben werden, dann wird genau dieser String auch wieder rausgeworfen.
        if (!is_numeric($saison)) {
            return $saison;
        }
        if ($saison == 25) {
            return "2020 (Corona-Saison)";
        }
        if ($saison > 25) {
            $saison_jahr = 1994 + $saison;
            $saison_jahr_next = $saison_jahr + 1;
            return substr($saison_jahr, -2) . "/" . substr($saison_jahr_next, -2);
        }
        return 1995 + $saison;
    }
    public static function platz_to_block($platz, $saison)
    {   
        if (empty($platz)){
            return '';
        }
        return Saison::get_saison_class($saison)::platz_to_block($platz, $saison);
    }
    //Weist dem Platz in der Rangtabelle eine Wertigkeit zu
    public static function platz_to_wertigkeit($platz, $saison = Config::SAISON)
    {
        if (empty($platz)){
            return '';
        }
        return Saison::get_saison_class($saison)::platz_to_wertigkeit($platz, $saison);
    }
}