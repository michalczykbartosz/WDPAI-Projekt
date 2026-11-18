<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\UserRepositoryInterface;

final class RegistrationService
{
    public function __construct(
        private readonly RegistrationValidator $validator,
        private readonly UserRepositoryInterface $users,
    ) {
    }

    /**
     * Rejestruje nowego zawodnika. Rola jest zawsze PLAYER – nie przyjmujemy jej z formularza.
     *
     * @return array<string, string> błędy walidacji; pusta tablica oznacza sukces
     */
    public function registerPlayer(string $email, string $displayName, string $password, string $passwordConfirmation): array
    {
        $errors = $this->validator->validate($email, $displayName, $password, $passwordConfirmation);

        if (!isset($errors['email']) && $this->users->emailExists($email)) {
            $errors['email'] = 'validation.email.taken';
        }

        if (!isset($errors['display_name']) && $this->users->displayNameExists($displayName)) {
            $errors['display_name'] = 'validation.display_name.taken';
        }

        if ($errors !== []) {
            return $errors;
        }

        $this->users->createPlayer($email, password_hash($password, PASSWORD_DEFAULT), $displayName);

        return [];
    }
}
