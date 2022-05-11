<?php

/**
 * Class PHPMailer
 */
class PHPMailer
{   
    /**
     * Lädt das PHPMailer-Framework und erstellt ein PHPMailer-Objekt
     */
    public static function load_phpmailer(): PHPMailer\PHPMailer\PHPMailer
    {
        require_once __DIR__ . '/../lib/composer/vendor/autoload.php';
        return new \PHPMailer\PHPMailer\PHPMailer();
    }
}