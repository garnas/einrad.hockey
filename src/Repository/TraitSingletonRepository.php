<?php

namespace App\Repository;

trait TraitSingletonRepository {

    private static self $instance;
    private static bool $isSetup = false;

    public static function isSetup(): bool
    {
        return self::$isSetup;
    }

    public static function setup(): void
    {
        self::$instance = new self();
        self::$isSetup = true;
    }

    public static function get(): self
    {
        if (!self::isSetup()) {
            self::setup();
        }
        return self::$instance;
    }

}