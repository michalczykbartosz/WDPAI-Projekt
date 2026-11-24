<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Powody odmowy zapisu na turniej. Wartość trafia do odpowiedzi JSON jako kod błędu,
 * a komunikat ma klucz registration.error.<wartość>.
 */
enum TournamentRegistrationError: string
{
    case NotFound = 'not_found';
    case Closed = 'registration_closed';
    case Full = 'tournament_full';
    case AlreadyRegistered = 'already_registered';

    public function httpStatus(): int
    {
        return match ($this) {
            self::NotFound => 404,
            self::Closed, self::Full, self::AlreadyRegistered => 409,
        };
    }
}
