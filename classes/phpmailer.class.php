<?php

class PHPMailer 
{   
    /**
     * Lädt das PHPMailer-Framework und erstellt ein PHPMailer-Objekt
     */
    public static function load_phpmailer()
    {
        require_once __DIR__ . '/../frameworks/composer/vendor/autoload.php';
        return new \PHPMailer\PHPMailer\PHPMailer();
    }
}