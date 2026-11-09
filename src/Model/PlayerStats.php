<?php

declare(strict_types=1);

namespace App\Model;

final readonly class PlayerStats
{
    public function __construct(
        public int $userId,
        public string $displayName,
        public ?string $avatarPath,
        public int $rank,
        public int $tournamentsPlayed,
        public int $matchesPlayed,
        public int $wins,
        public int $losses,
        public float $winRate,
        public ?float $threeDartAverage,
        public int $total180s,
        public ?int $bestCheckout,
        public int $tournamentsWon,
        public int $points,
    ) {
    }
}
