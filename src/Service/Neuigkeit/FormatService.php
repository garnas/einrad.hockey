<?php

namespace App\Service\Neuigkeit;
use App\Enum\NeuigkeitArt;

class FormatService
{
    public static function getTimespan(\DateTimeInterface $time): string
    {
        $delta = (time() - $time->getTimestamp()) / 3600; // in hours
        
        if ($delta < 1) {
            return "gerade eben";
        }

        if ($delta < 2) {
            return "vor einer Stunde";
        }

        if ($delta < 24) {
            return "vor " . round($delta) . " Stunden";
        }

        if ($delta < 2*24) {
            return "vor einem Tag";
        }

        if ($delta < 7*24) {
            return "vor " . round($delta / 24) . " Tagen";
        }

        return $time->format('d.m.Y');
    }


    public static function getArtString(NeuigkeitArt $art): string
    {
        $text = $art->value;
        $text = str_replace("oe", "ö", $text);
        $text = str_replace("ae", "ä", $text);
        $text = str_replace("ue", "ü", $text);
        $text = ucfirst($text);
        return $text;
    }

    
    /**
     * Wandelt die Zeichen in den Neuigkeiten-Einträgen um, damit sie HTML-Entities sind
     *
     * @param array $neuigkeiten
     * @return array
     */
    private static function characters(array $neuigkeiten): array
    {
        foreach ($neuigkeiten as $key => $neuigkeit) {
            if (
                $neuigkeit['eingetragen_von'] === 'Ligaausschuss'
                || $neuigkeit['eingetragen_von'] === "Öffentlichkeitsausschuss"
                || $neuigkeit['eingetragen_von'] === "Nationalkader"
            ) {
                $neuigkeiten[$key]['inhalt'] = htmlspecialchars_decode($neuigkeit['inhalt'], ENT_QUOTES);
                
                if (isset($neuigkeit['titel'])) {
                    // Titel wird nur dekodiert, wenn er gesetzt ist
                    $neuigkeiten[$key]['titel'] = htmlspecialchars_decode($neuigkeit['titel'], ENT_QUOTES);
                }
            }
        }

        return $neuigkeiten;
    }
}
