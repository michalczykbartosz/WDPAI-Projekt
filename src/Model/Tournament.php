<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final readonly class Tournament
{
    public function __construct(
        public int $id,
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
        public string $inviteCode,
        public TournamentStatus $status,
        public int $registeredPlayers = 0,
    ) {
    }

    public function format(): string
    {
        return $this->startingScore . ' ' . $this->finishRule->label();
    }

    public function isOwnedBy(int $userId): bool
    {
        return $this->organizerId === $userId;
    }

    public function isRegistrationOpen(DateTimeImmutable $now): bool
    {
        return $this->status === TournamentStatus::Open && $now <= $this->registrationDeadline;
    }
}
