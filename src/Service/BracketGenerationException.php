<?php

declare(strict_types=1);

namespace App\Service;

final class BracketGenerationException extends \DomainException
{
    public function __construct(public readonly BracketGenerationError $error)
    {
        parent::__construct('Bracket generation refused: ' . $error->value);
    }
}
