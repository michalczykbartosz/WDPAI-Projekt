<?php

declare(strict_types=1);

namespace App\Model;

use DateTimeImmutable;

final readonly class GameMatch
{
    public function __construct(
        public int $id,
        public int $tournamentId,
        public int $round,
        public int $position,
        public ?int $player1Id,
        public ?string $player1Name,
        public ?int $player2Id,
        public ?string $player2Name,
        public ?int $player1Legs,
        public ?int $player2Legs,
        public ?int $winnerId,
        public MatchStatus $status,
        public ?DateTimeImmutable $playedAt,
    ) {
    }

    public function isFinished(): bool
    {
        return $this->status === MatchStatus::Finished;
    }

    /** Wolny los: mecz zakończony bez przeciwnika, gracz przechodzi dalej bez gry. */
    public function isBye(): bool
    {
        return $this->isFinished() && ($this->player1Id === null || $this->player2Id === null);
    }

    public function hasPlayer(int $userId): bool
    {
        return $this->player1Id === $userId || $this->player2Id === $userId;
    }

    public function nextRoundPosition(): int
    {
        return intdiv($this->position + 1, 2);
    }
}
