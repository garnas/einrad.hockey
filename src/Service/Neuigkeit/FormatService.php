<?php

namespace App\Service\Neuigkeit;

class FormatService
{
    public static function getTimespan(\DateTimeInterface $time): string
    {
        $delta = (time() - $time->getTimestamp()) / 3600; // in hours
        
        if ($delta < 1) {
            return "gerade eben";
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
}
