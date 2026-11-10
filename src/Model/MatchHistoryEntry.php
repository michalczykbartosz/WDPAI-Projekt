<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final readonly class MatchHistoryEntry
{
    public function __construct(
        public int $matchId,
        public int $tournamentId,
        public string $tournamentName,
        public DateTimeImmutable $playedAt,
        public ?int $opponentId,
        public ?string $opponentName,
        public ?int $playerLegs,
        public ?int $opponentLegs,
        public bool $won,
        public ?float $threeDartAverage,
        public ?int $count180s,
        public ?int $highestCheckout,
    ) {
    }

    public function score(): string
    {
        return ($this->playerLegs ?? '-') . ':' . ($this->opponentLegs ?? '-');
    }
}
