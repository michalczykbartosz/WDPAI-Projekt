<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final readonly class User
{
    public function __construct(
        public int $id,
        public string $email,
        private string $passwordHash,
        public UserRole $role,
        public DateTimeImmutable $createdAt,
    ) {
    }

    public function verifyPassword(string $plainPassword): bool
    {
        return password_verify($plainPassword, $this->passwordHash);
    }

    public function isOrganizer(): bool
    {
        return $this->role === UserRole::Organizer;
    }
}
