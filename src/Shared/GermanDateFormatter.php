<?php

namespace App\Shared;

use DateTimeInterface;
use IntlDateFormatter;

/**
 * Einheitliche deutsche Datumsformatierung via IntlDateFormatter.
 */
class GermanDateFormatter
{
    /** @var array<string, IntlDateFormatter> */
    private static array $formatters = [];

    /** $date: DateTime-Objekt oder Unix-Timestamp */
    public static function format(DateTimeInterface|int $date, string $pattern): string
    {
        self::$formatters[$pattern] ??= new IntlDateFormatter("de_DE", pattern: $pattern);
        return self::$formatters[$pattern]->format($date);
    }

    /**
     * Formatiert einen beliebigen strtotime-lesbaren Datumsstring, z. B. "01.09.2026", "2026-09-01" oder "now".
     */
    public static function formatString(string $date, string $pattern): string
    {
        $timestamp = strtotime($date);
        return self::format($timestamp, $pattern);
    }

    /** z. B. "Oktober" */
    public static function month(DateTimeInterface $date): string
    {
        return self::format($date, "MMMM");
    }
}
