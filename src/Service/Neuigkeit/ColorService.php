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
            default:
                return 'w3-primary';
        }
    }
}
