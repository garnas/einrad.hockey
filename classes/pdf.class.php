<?php

class pdf 
{   
    //Lädt das Mpdf-Framework und erstellt ein mpdf-Objekt
    public static function start_mpdf()
    {
        require_once __DIR__ . "/../frameworks/mpdf/vendor/autoload.php";
        return new \Mpdf\Mpdf(['mode' => 'utf-8', 'format' => 'A4-P']);
    }
}