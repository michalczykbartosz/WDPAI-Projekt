<?php

declare(strict_types=1);

namespace App\Service;

final class MatchResultException extends \DomainException
{
    public function __construct(public readonly MatchResultError $error)
    {
        parent::__construct('Match result refused: ' . $error->value);
    }
}
