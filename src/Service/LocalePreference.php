<?php

declare(strict_types=1);

namespace App\Service;

use App\Security\SessionManager;

/**
 * Przechowuje wybrany język w sesji. Wartość z sesji jest zawsze sprawdzana
 * z listą obsługiwanych języków, zanim trafi do Translatora.
 */
final class LocalePreference
{
    private const SESSION_KEY = 'locale';

    public function __construct(private readonly SessionManager $session)
    {
    }

    public function current(): string
    {
        $locale = $this->session->get(self::SESSION_KEY);

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

        $this->session->set(self::SESSION_KEY, $locale);
    }
}
