<?php

namespace App\Service\Neuigkeit;
use App\Enum\NeuigkeitArt;

class ColorService
{
    public static function getColor(NeuigkeitArt $art): string
    {
        switch($art) {
            case NeuigkeitArt::FOERDERMITTEL:
                return 'ehl-green';
            case NeuigkeitArt::ADVENTSKALENDER:
                return 'ehl-red';
            default:
                return 'w3-primary';
        }
    }
}
