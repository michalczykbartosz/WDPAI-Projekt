<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

/**
 * Dane nowego turnieju po walidacji, gotowe do zapisu w bazie.
 * Identyfikator, kod zaproszenia i status nadaje dopiero repozytorium.
 */
final readonly class NewTournament
{
    public function __construct(
        public int $organizerId,
        public string $name,
        public ?string $description,
        public string $location,
        public DateTimeImmutable $startsAt,
        public DateTimeImmutable $registrationDeadline,
        public int $maxPlayers,
        public int $startingScore,
        public FinishRule $finishRule,
        public int $legsToWin,
    ) {
    }
}
