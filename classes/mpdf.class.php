<?php

class mPDF
{
    /**
     * Lädt das Mpdf-Framework und erstellt ein mpdf-Objekt
     */
    public static function load_mpdf(): \Mpdf\Mpdf
    {

        $defaultConfig = (new Mpdf\Config\ConfigVariables())->getDefaults();
        $fontDirs = $defaultConfig['fontDir'];

        $defaultFontConfig = (new Mpdf\Config\FontVariables())->getDefaults();
        $fontData = $defaultFontConfig['fontdata'];

        return new \Mpdf\Mpdf(
            [
                'mode' => 'utf-8',
                'format' => 'A4-P',
            ]);
    }
}