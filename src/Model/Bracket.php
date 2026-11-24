<?php

declare(strict_types=1);

namespace App\Model;

/**
 * Wylosowana drabinka: wszystkie mecze wszystkich rund oraz numery rozstawienia
 * graczy w kolejności losowania.
 */
final readonly class Bracket
{
    /**
     * @param list<PlannedMatch> $matches
     * @param array<int, int> $seeds identyfikator gracza => numer rozstawienia (od 1)
     */
    public function __construct(
        public int $size,
        public array $matches,
        public array $seeds,
    ) {
    }

    public function rounds(): int
    {
        return (int) log($this->size, 2);
    }

    /** @return list<PlannedMatch> */
    public function matchesInRound(int $round): array
    {
        return array_values(array_filter(
            $this->matches,
            static fn (PlannedMatch $match): bool => $match->round === $round,
        ));
    }
}
