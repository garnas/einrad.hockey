<?php

namespace App\Service\Abstimmung;

class ConfigService
{
    public const NAMES = [
        "person_a" => ['name' => "Dominik Bilitewski", 'team' => "Hockey Hawks"],
        "person_b" => ['name' => "Philipp Gross", 'team' => "SKV Mörfelden Titans"],
        "person_c" => ['name' => "Ole Jaekel", 'team' => "Dresdner Einradlöwen"],
        "person_d" => ['name' => "Janina Lenz", 'team' => "TV Lilienthal Moorteufel"],
        "person_e" => ['name' => "Lukas Schollähn", 'team' => "Hockey Hawks"],
        "person_f" => ['name' => "Günther Schumacher", 'team' => "Uniwheeler Bremen-Bochum"],
        "person_g" => ['name' => "Isabell Schumacher", 'team' => "New Angels"],
        "person_h" => ['name' => "Fin Thiessen", 'team' => "MUH-Bande Jena"],
        "person_i" => ['name' => "Helge Wagner", 'team' => "Einradfüchse"],
    ];

    public const BEGINN = "15.02.2026 00:00";
    public const ENDE = "01.03.2026 23:59";

    public static function isPreparing(): bool
    {
        return time() < strtotime(self::BEGINN);
    }

    public static function isRunning(): bool
    {
        return (strtotime(self::BEGINN) <= time() && time() < strtotime(self::ENDE));
    }

    public static function isFinished(): bool
    {
        return strtotime(self::ENDE) <= time();
    }
}
