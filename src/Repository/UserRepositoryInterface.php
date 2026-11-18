<?php

declare(strict_types=1);

namespace App\Repository;

interface UserRepositoryInterface
{
    public function emailExists(string $email): bool;

    public function displayNameExists(string $displayName): bool;

    /** Tworzy konto zawodnika razem z profilem i zwraca id nowego użytkownika. */
    public function createPlayer(string $email, string $passwordHash, string $displayName): int;
}
