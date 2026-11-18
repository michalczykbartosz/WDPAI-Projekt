<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Jeden losowy token na sesję. Formularze wysyłają go w ukrytym polu _csrf,
 * a żądania Fetch w nagłówku X-CSRF-Token. Obca strona nie zna tokenu,
 * więc nie podrobi żądania w imieniu zalogowanego użytkownika.
 */
final class CsrfTokenManager
{
    public const FIELD_NAME = '_csrf';
    private const SESSION_KEY = 'csrf_token';

    public function __construct(private readonly SessionManager $session)
    {
    }

    public function token(): string
    {
        $token = $this->session->get(self::SESSION_KEY);

        if (!is_string($token) || $token === '') {
            $token = bin2hex(random_bytes(32));
            $this->session->set(self::SESSION_KEY, $token);
        }

        return $token;
    }

    public function isValid(mixed $submittedToken): bool
    {
        $expectedToken = $this->session->get(self::SESSION_KEY);

        return is_string($expectedToken)
            && is_string($submittedToken)
            && hash_equals($expectedToken, $submittedToken);
    }

    public function tokenFromRequest(): mixed
    {
        return $_POST[self::FIELD_NAME] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;
    }
}
