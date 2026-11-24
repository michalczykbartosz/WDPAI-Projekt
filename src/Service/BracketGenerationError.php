<?php

declare(strict_types=1);

namespace App\Service;

/** Powody odmowy wygenerowania drabinki; komunikat ma klucz bracket.error.<wartość>. */
enum BracketGenerationError: string
{
    case NotFound = 'not_found';
    case NotOwner = 'not_owner';
    case AlreadyGenerated = 'already_generated';
    case NotEnoughPlayers = 'not_enough_players';
}
