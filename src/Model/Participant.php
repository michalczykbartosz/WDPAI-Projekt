<?php

declare(strict_types=1);

namespace App\Model;

final readonly class Participant
{
    public function __construct(
        public int $userId,
        public string $displayName,
        public ?int $seed,
        public ?float $threeDartAverage,
    ) {
    }
}
