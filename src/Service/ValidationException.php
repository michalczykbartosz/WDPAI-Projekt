<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Rzucany przez serwis, gdy dane z formularza są niepoprawne.
 * Niesie błędy jako pole formularza => klucz tłumaczenia.
 */
final class ValidationException extends \DomainException
{
    /** @param array<string, string> $errors */
    public function __construct(public readonly array $errors)
    {
        parent::__construct('Validation failed.');
    }
}
