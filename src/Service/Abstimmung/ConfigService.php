<?php

namespace App\Service\Abstimmung;

class ConfigService
{
    public const NAMES = [
        "person_a" => array('name' => "Dominik Bilitewski", 'team' => "Hockey Hawks"),
        "person_b" => array('name' => "Philipp Gross", 'team' => "SKV Mörfelden Titans"),
        "person_c" => array('name' => "Ole Jaekel", 'team' => "Dresdner Einradlöwen"),
        "person_d" => array('name' => "Janina Lenz", 'team' => "TV Lilienthal Moorteufel"),
        "person_e" => array('name' => "Lukas Schollähn", 'team' => "Hockey Hawks"),
        "person_f" => array('name' => "Günther Schumacher", 'team' => "Uniwheeler Bremen-Bochum"),
        "person_g" => array('name' => "Isabell Schumacher", 'team' => "New Angels"),
        "person_h" => array('name' => "Fin Thiessen", 'team' => "MUH-Bande Jena"),
        "person_i" => array('name' => "Helge Wagner", 'team' => "Einradfüchse"),
    ];

    public const BEGINN = "15.02.2026 00:00";
    public const ENDE = "01.03.2026 23:59";
    public const KEY = 'PMYaWnctK1407nyu';

    public static function isPreparing(): bool {
        return time() < strtotime(ConfigService::BEGINN);
    }

    public static function isRunning(): bool {
        return (strtotime(ConfigService::BEGINN) <= time() && time() < strtotime(ConfigService::ENDE));
    }

    public static function isFinished(): bool {
        return strtotime(ConfigService::ENDE) <= time();
    }
}
