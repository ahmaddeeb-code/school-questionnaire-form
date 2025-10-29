<?php
namespace App\Helpers;

class I18n
{
    private static array $messages = [];

    public static function init(string $locale, array $availableLocales, string $default): void
    {
        if (!in_array($locale, $availableLocales, true)) {
            $locale = $default;
        }
        self::load($locale);
        $_SESSION['locale'] = $locale;
    }

    public static function load(string $locale): void
    {
        $path = __DIR__ . '/../../resources/lang/' . $locale . '.json';
        if (!file_exists($path)) {
            $path = __DIR__ . '/../../resources/lang/en.json';
        }
        $json = file_get_contents($path);
        self::$messages = json_decode($json, true) ?? [];
        $_SESSION['is_rtl'] = ($locale === 'ar');
    }

    public static function translate(string $key, array $replace = []): string
    {
        $message = self::$messages[$key] ?? $key;
        foreach ($replace as $search => $value) {
            $message = str_replace(':' . $search, $value, $message);
        }
        return $message;
    }

    public static function locale(): string
    {
        return $_SESSION['locale'] ?? 'en';
    }

    public static function isRtl(): bool
    {
        return (bool)($_SESSION['is_rtl'] ?? false);
    }
}
