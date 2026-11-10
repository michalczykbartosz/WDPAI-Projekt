<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final readonly class PlayerProfile
{
    public function __construct(
        public int $userId,
        public string $displayName,
        public ?string $club,
        public ?string $avatarPath,
        public DateTimeImmutable $memberSince,
    ) {
    }
}
