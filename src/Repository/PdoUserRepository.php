<?php

declare(strict_types=1);

namespace App\Repository;

final class PdoUserRepository extends PdoRepository implements UserRepositoryInterface
{
    public function emailExists(string $email): bool
    {
        $row = $this->fetchOne(
            'SELECT EXISTS (SELECT 1 FROM users WHERE lower(email) = lower(:email)) AS taken',
            ['email' => $email],
        );

        return (bool) $row['taken'];
    }

    public function displayNameExists(string $displayName): bool
    {
        $row = $this->fetchOne(
            'SELECT EXISTS (SELECT 1 FROM player_profiles WHERE lower(display_name) = lower(:display_name)) AS taken',
            ['display_name' => $displayName],
        );

        return (bool) $row['taken'];
    }

    public function createPlayer(string $email, string $passwordHash, string $displayName): int
    {
        return $this->transaction(function () use ($email, $passwordHash, $displayName): int {
            $row = $this->fetchOne(
                "INSERT INTO users (email, password_hash, role) VALUES (:email, :password_hash, 'PLAYER') RETURNING id",
                ['email' => $email, 'password_hash' => $passwordHash],
            );
            $userId = (int) $row['id'];

            $this->execute(
                'INSERT INTO player_profiles (user_id, display_name) VALUES (:user_id, :display_name)',
                ['user_id' => $userId, 'display_name' => $displayName],
            );

            return $userId;
        });
    }
}
