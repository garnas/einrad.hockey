<?php

class mpdf 
{   
    /**
     * Lädt das Mpdf-Framework und erstellt ein mpdf-Objekt
     */
    public static function load_mpdf()
    {
        require_once __DIR__ . "/../frameworks/composer/vendor/autoload.php";
        return new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
    }
}