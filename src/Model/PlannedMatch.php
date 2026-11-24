<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Mecz zaplanowany przez generator drabinki, jeszcze przed zapisem w bazie.
 * Wolny los to mecz z jednym graczem, który od razu jest zwycięzcą.
 */
final readonly class PlannedMatch
{
    public function __construct(
        public int $round,
        public int $position,
        public ?int $player1Id,
        public ?int $player2Id,
        public ?int $winnerId = null,
    ) {
    }

    public function isBye(): bool
    {
        return $this->winnerId !== null && ($this->player1Id === null || $this->player2Id === null);
    }
}
