<?php

declare(strict_types=1);

namespace App\Service;

final class Translator
{
    public const SUPPORTED_LOCALES = ['pl', 'en'];
    public const DEFAULT_LOCALE = 'pl';

    /** @var array<string, string> */
    private array $messages;

    public function __construct(
        private readonly string $locale,
        string $dictionaryDir,
    ) {
        if (!self::isSupported($locale)) {
            throw new \InvalidArgumentException('Unsupported locale: ' . $locale);
        }

        $this->messages = require $dictionaryDir . '/' . $locale . '.php';
    }

    public static function isSupported(string $locale): bool
    {
        return in_array($locale, self::SUPPORTED_LOCALES, true);
    }

    public function locale(): string
    {
        return $this->locale;
    }

    /**
     * Zwraca tekst dla klucza i podstawia parametry w formacie {nazwa}.
     * Brakujący klucz jest zwracany bez zmian, żeby od razu było go widać w interfejsie.
     *
     * @param array<string, string|int|float> $parameters
     */
    public function get(string $key, array $parameters = []): string
    {
        $message = $this->messages[$key] ?? $key;

        $replacements = [];
        foreach ($parameters as $name => $value) {
            $replacements['{' . $name . '}'] = (string) $value;
        }

        return strtr($message, $replacements);
    }
}
