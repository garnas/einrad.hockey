<?php

/**
 * Class Captcha
 * Implementiert die Gregwar-Captcha-Libary
 * https://github.com/Gregwar/Captcha
 */
class Captcha
{
    /**
     * Lädt die Gregwar/Captcha Klasse
     *
     */
    public static function load(): Gregwar\Captcha\CaptchaBuilder
    {
        // Zufällige Phrases mit Themenbezug, welche erlesen werden müssen
        $phrases = ['einrad', 'hockey', 'ball', 'liga', 'meister', 'sib', 'sub', 'tor', 'ecke', 'schiri', 'turnier'];
        // Captchas werden in der Session gespeichert, ansonsten wenn kein Captcha in der Session, dann ein Zufälliges
        $phrase = $_SESSION['captcha'] ?? array_rand(array_flip($phrases));

        $captcha = new Gregwar\Captcha\CaptchaBuilder($phrase);
        $captcha->build(200, 80);

        return $captcha;
    }
}
