<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Przechowuje wybrany język w sesji. Wartość z sesji jest zawsze sprawdzana
 * z listą obsługiwanych języków, zanim trafi do Translatora.
 */
final class LocalePreference
{
    private const SESSION_KEY = 'locale';

    public function current(): string
    {
        $locale = $_SESSION[self::SESSION_KEY] ?? null;

        if (is_string($locale) && Translator::isSupported($locale)) {
            return $locale;
        }

        return Translator::DEFAULT_LOCALE;
    }

    public function change(string $locale): void
    {
        if (!Translator::isSupported($locale)) {
            throw new \InvalidArgumentException('Unsupported locale: ' . $locale);
        }

        $_SESSION[self::SESSION_KEY] = $locale;
    }
}
