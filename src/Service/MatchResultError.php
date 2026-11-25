<?php

declare(strict_types=1);

namespace App\Service;

/** Powody odrzucenia wyniku meczu; komunikat ma klucz result.error.<wartość>. */
enum MatchResultError: string
{
    case NotFound = 'not_found';
    case Forbidden = 'forbidden';
    case NotPlayable = 'not_playable';

    public function httpStatus(): int
    {
        return match ($this) {
            self::NotFound => 404,
            self::Forbidden => 403,
            self::NotPlayable => 409,
        };
    }
}
