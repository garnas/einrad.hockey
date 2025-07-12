<?php
require_once __DIR__ . '/../env.php';

spl_autoload_register(
    static function ($class) {
        $class = strtolower($class);
        $array = explode('\\', $class);
        $className = $array[array_key_last($array)];
        $path = Env::BASE_PATH . '/classes/' . $className . '.class.php';
        if (file_exists($path)) {
            include $path;
        }
    }
);

require_once __DIR__ . '/../vendor/autoload.php';