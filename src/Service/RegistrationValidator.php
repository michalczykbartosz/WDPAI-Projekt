<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Sprawdza format danych rejestracji. Zwraca błędy jako klucze tłumaczeń,
 * dzięki czemu komunikaty pojawiają się w wybranym języku.
 */
final class RegistrationValidator
{
    public const DISPLAY_NAME_MIN_LENGTH = 3;
    public const DISPLAY_NAME_MAX_LENGTH = 50;
    public const PASSWORD_MIN_LENGTH = 8;
    public const PASSWORD_MAX_BYTES = 72;
    private const EMAIL_MAX_LENGTH = 255;

    /** @return array<string, string> pole formularza => klucz komunikatu */
    public function validate(string $email, string $displayName, string $password, string $passwordConfirmation): array
    {
        return array_filter([
            'email' => $this->validateEmail($email),
            'display_name' => $this->validateDisplayName($displayName),
            'password' => $this->validatePassword($password),
            'password_confirmation' => $password !== $passwordConfirmation ? 'validation.password.mismatch' : null,
        ]);
    }

    private function validateEmail(string $email): ?string
    {
        if ($email === '') {
            return 'validation.required';
        }

        if (mb_strlen($email) > self::EMAIL_MAX_LENGTH || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            return 'validation.email.invalid';
        }

        return null;
    }

    private function validateDisplayName(string $displayName): ?string
    {
        if ($displayName === '') {
            return 'validation.required';
        }

        $length = mb_strlen($displayName);

        if ($length < self::DISPLAY_NAME_MIN_LENGTH || $length > self::DISPLAY_NAME_MAX_LENGTH) {
            return 'validation.display_name.length';
        }

        return null;
    }

    private function validatePassword(string $password): ?string
    {
        if ($password === '') {
            return 'validation.required';
        }

        if (mb_strlen($password) < self::PASSWORD_MIN_LENGTH) {
            return 'validation.password.too_short';
        }

        // bcrypt bierze pod uwagę tylko pierwsze 72 bajty hasła
        if (strlen($password) > self::PASSWORD_MAX_BYTES) {
            return 'validation.password.too_long';
        }

        return null;
    }
}
