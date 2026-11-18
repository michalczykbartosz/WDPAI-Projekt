<?php

declare(strict_types=1);

namespace App\Security;

final class SessionManager
{
    private const COOKIE_NAME = 'DARTARENA_SESSION';
    private const FLASH_KEY = 'flash';

    public function start(): void
    {
        if (session_status() === PHP_SESSION_ACTIVE) {
            return;
        }

        session_name(self::COOKIE_NAME);
        session_start([
            'cookie_lifetime' => 0,
            'cookie_path' => '/',
            'cookie_secure' => $this->isHttps(),
            'cookie_httponly' => true,
            'cookie_samesite' => 'Lax',
            'use_strict_mode' => true,
            'use_only_cookies' => true,
            'use_trans_sid' => false,
        ]);
    }

    /**
     * Wywoływane po zalogowaniu: nowy identyfikator sesji chroni przed
     * przejęciem sesji (session fixation), a stary jest od razu usuwany.
     */
    public function regenerate(): void
    {
        session_regenerate_id(true);
    }

    public function get(string $key, mixed $default = null): mixed
    {
        return $_SESSION[$key] ?? $default;
    }

    public function set(string $key, mixed $value): void
    {
        $_SESSION[$key] = $value;
    }

    public function remove(string $key): void
    {
        unset($_SESSION[$key]);
    }

    /** Zapamiętuje komunikat, który zostanie pokazany raz, na następnej stronie. */
    public function flash(string $messageKey): void
    {
        $this->set(self::FLASH_KEY, $messageKey);
    }

    public function pullFlash(): ?string
    {
        $messageKey = $this->get(self::FLASH_KEY);
        $this->remove(self::FLASH_KEY);

        return is_string($messageKey) ? $messageKey : null;
    }

    public function destroy(): void
    {
        $_SESSION = [];

        $params = session_get_cookie_params();
        setcookie(session_name(), '', [
            'expires' => time() - 3600,
            'path' => $params['path'],
            'domain' => $params['domain'],
            'secure' => $params['secure'],
            'httponly' => $params['httponly'],
            'samesite' => $params['samesite'],
        ]);

        session_destroy();
    }

    private function isHttps(): bool
    {
        return ($_SERVER['HTTPS'] ?? 'off') !== 'off';
    }
}
