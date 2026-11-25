<?php

declare(strict_types=1);

namespace App\Model;

/** Sprawdzony wynik meczu gotowy do zapisu: legi i statystyki obu graczy. */
final readonly class MatchResult
{
    public function __construct(
        public int $player1Legs,
        public int $player2Legs,
        public PlayerMatchStats $player1Stats,
        public PlayerMatchStats $player2Stats,
    ) {
    }

    public function player1Won(): bool
    {
        return $this->player1Legs > $this->player2Legs;
    }
}
