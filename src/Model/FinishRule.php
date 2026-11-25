<?php

declare(strict_types=1);

namespace App\Model;

enum FinishRule: string
{
    case DoubleOut = 'double_out';
    case SingleOut = 'single_out';

    /**
     * Wyników z tej listy nie da się zamknąć trzema lotkami, choć mieszczą się w zakresie.
     * Double Out: ostatnia lotka musi trafić w podwójne pole (maks. 170 = T20 T20 Bull).
     * Single Out: dowolne pole (maks. 180 = T20 T20 T20).
     */
    private const IMPOSSIBLE_CHECKOUTS = [
        'double_out' => [169, 168, 166, 165, 163, 162, 159],
        'single_out' => [179, 178, 176, 175, 173, 172, 169, 166, 163],
    ];

    public function label(): string
    {
        return match ($this) {
            self::DoubleOut => 'Double Out',
            self::SingleOut => 'Single Out',
        };
    }

    public function minCheckout(): int
    {
        return match ($this) {
            self::DoubleOut => 2,
            self::SingleOut => 1,
        };
    }

    public function maxCheckout(): int
    {
        return match ($this) {
            self::DoubleOut => 170,
            self::SingleOut => 180,
        };
    }

    public function isPossibleCheckout(int $points): bool
    {
        return $points >= $this->minCheckout()
            && $points <= $this->maxCheckout()
            && !in_array($points, self::IMPOSSIBLE_CHECKOUTS[$this->value], true);
    }
}
