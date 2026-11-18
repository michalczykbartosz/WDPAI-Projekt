<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\User;
use App\Repository\UserRepositoryInterface;

final class LoginService
{
    /**
     * Hash losowego hasła. Gdy konto nie istnieje, i tak liczymy password_verify,
     * żeby czas odpowiedzi nie zdradzał, czy dany e-mail jest zarejestrowany.
     */
    private const DUMMY_HASH = '$2y$10$z1J8L8RHXRmwZ/FIIR882OFA4vOhR4WA1sbzuE7nCpF0VTM2j9jzq';

    public function __construct(private readonly UserRepositoryInterface $users)
    {
    }

    public function authenticate(string $email, string $password): ?User
    {
        $user = $this->users->findByEmail($email);

        if ($user === null) {
            password_verify($password, self::DUMMY_HASH);
            return null;
        }

        return $user->verifyPassword($password) ? $user : null;
    }
}
