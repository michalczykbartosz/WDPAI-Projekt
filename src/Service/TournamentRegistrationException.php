<?php

declare(strict_types=1);

namespace App\Service;

final class TournamentRegistrationException extends \DomainException
{
    public function __construct(public readonly TournamentRegistrationError $error)
    {
        parent::__construct('Tournament registration refused: ' . $error->value);
    }
}
