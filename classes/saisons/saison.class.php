<?php
class Saison {

    public static function get_saison_class($saison)
    {
        $class_map = array(
            '26'=>NEW Saison_26(),
            '25'=>NEW Saison_25(),
            '24'=>NEW Saison_24(),
            '23'=>NEW Saison_23(),
            '22'=>NEW Saison_22(),
            '21'=>NEW Saison_21(),
            '20'=>NEW Saison_20(),
            '19'=>NEW Saison_19(),
            '18'=>NEW Saison_18(),
            '17'=>NEW Saison_17(),
            '16'=>NEW Saison_16(),
            '15'=>NEW Saison_15(),
            '14'=>NEW Saison_14(),
            '13'=>NEW Saison_13(),
            //'12'=>NEW Saison_12(),
            //'11'=>NEW Saison_11(),
            //'10'=>NEW Saison_10(),
            //'09'=>NEW Saison_09(),
            //'08'=>NEW Saison_08(),
            //'07'=>NEW Saison_07(),
            //'06'=>NEW Saison_06(),
            //'05'=>NEW Saison_05(),
            //'04'=>NEW Saison_04(),
            //'03'=>NEW Saison_03(),
            //'02'=>NEW Saison_02(),
            //'01'=>NEW Saison_01(),
            //'00'=>NEW Saison_00(),
        );
        return $class_map[$saison];
    }

    //Ligagebühr
    public static function get_ligagebuehr($saison = Config::SAISON)
    {
        return Saison::get_saison_class($saison)::get_ligagebuehr();
    }
    //SAISON_ANFANG
    public static function get_saison_anfang($saison = Config::SAISON)
    {
        return Saison::get_saison_class($saison)::get_saison_anfang();
    }
    //SAISON_ENDE
    public static function get_saison_ende($saison = Config::SAISON)
    {
        return Saison::get_saison_class($saison)::get_saison_ende();
    }
    ///////////////////////////////////////////////////////////////
    ///////////////////////////Teamblöcke//////////////////////////
    ///////////////////////////////////////////////////////////////
    //Für die Block und Wertzuordnung in der Rangtabelle siehe Saison::platz_to_block und Saison::platz_to_wertigkeit
    //Reihenfolge bei den Blöcken muss immer hoch -> niedrig sein

    //Mögliche Team-Blöcke
    public static function get_block($saison = Config::SAISON)
    {
        return Saison::get_saison_class($saison)::get_block();
    }
    //Mögliche Turnier-Blöcke 
    public static function get_block_all($saison = Config::SAISON)
    {
        return Saison::get_saison_class($saison)::get_block_all();
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