<?php
namespace App\Helpers;

class Validate
{
    public static function required(string $value): bool
    {
        return trim($value) !== '';
    }

    public static function email(string $value): bool
    {
        return filter_var($value, FILTER_VALIDATE_EMAIL) !== false;
    }

    public static function maxLength(string $value, int $max): bool
    {
        return mb_strlen($value) <= $max;
    }

    public static function minLength(string $value, int $min): bool
    {
        return mb_strlen($value) >= $min;
    }
}
