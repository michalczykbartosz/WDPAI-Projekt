<?php

declare(strict_types=1);

namespace App\Security;

use App\Model\User;
use App\Model\UserRole;

/**
 * Tożsamość zalogowanego użytkownika trzymana w sesji po stronie serwera.
 * Rola pochodzi z bazy w chwili logowania, nigdy z danych wysłanych przez klienta.
 */
final class AuthSession
{
    private const USER_ID = 'auth_user_id';
    private const ROLE = 'auth_role';
    private const DISPLAY_NAME = 'auth_display_name';

    public function __construct(private readonly SessionManager $session)
    {
    }

    public function logIn(User $user): void
    {
        $this->session->regenerate();
        $this->session->set(self::USER_ID, $user->id);
        $this->session->set(self::ROLE, $user->role->value);
        $this->session->set(self::DISPLAY_NAME, $user->displayName ?? $user->email);
    }

    public function isLoggedIn(): bool
    {
        return $this->userId() !== null;
    }

    public function userId(): ?int
    {
        $userId = $this->session->get(self::USER_ID);

        return is_int($userId) ? $userId : null;
    }

    public function role(): ?UserRole
    {
        $role = $this->session->get(self::ROLE);

        return is_string($role) ? UserRole::tryFrom($role) : null;
    }

    public function displayName(): ?string
    {
        $displayName = $this->session->get(self::DISPLAY_NAME);

        return is_string($displayName) ? $displayName : null;
    }
}
