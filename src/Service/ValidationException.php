<?php

declare(strict_types=1);

namespace App\Service;

/**
 * Rzucany przez serwis, gdy dane z formularza są niepoprawne.
 * Niesie błędy jako pole formularza => klucz tłumaczenia oraz parametry
 * podstawiane do komunikatów (np. {legs}).
 */
final class ValidationException extends \DomainException
{
    /**
     * @param array<string, string> $errors
     * @param array<string, string|int> $parameters
     */
    public function __construct(
        public readonly array $errors,
        public readonly array $parameters = [],
    ) {
        parent::__construct('Validation failed.');
    }
}
