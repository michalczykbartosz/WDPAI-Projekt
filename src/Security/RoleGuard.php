<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\UserRole;

/**
 * Sprawdza rolę zalogowanego użytkownika na podstawie sesji po stronie serwera.
 * Rola trafia do sesji z bazy przy logowaniu, więc klient nie może jej podmienić.
 */
final class RoleGuard
{
    public function __construct(private readonly AuthSession $auth)
    {
    }

    public function check(UserRole ...$allowedRoles): AccessDecision
    {
        $role = $this->auth->role();

        if (!$this->auth->isLoggedIn() || $role === null) {
            return AccessDecision::Unauthenticated;
        }

        return in_array($role, $allowedRoles, true)
            ? AccessDecision::Granted
            : AccessDecision::Forbidden;
    }
}
