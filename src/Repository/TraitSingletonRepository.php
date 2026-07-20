<?php

namespace App\Repository;

trait TraitSingletonRepository
{
    private static ?self $instance = null;

    public static function get(): self
    {
        return self::$instance ??= new self();
    }
}
