<?php

namespace App\Shared;

trait SingletonRepositoryTrait
{
    private static ?self $instance = null;

    public static function get(): self
    {
        return self::$instance ??= new self();
    }
}
