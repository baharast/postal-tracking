<?php

namespace App\Traits;

trait Enumable
{
    public static function values(): array
    {
        return array_map(fn($case) => $case->value, static::cases());
    }
}
