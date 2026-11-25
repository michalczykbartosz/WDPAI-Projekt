<?php

declare(strict_types=1);

namespace App\Model;

/** Statystyki jednego gracza z jednego meczu (tabela match_statistics). */
final readonly class PlayerMatchStats
{
    public function __construct(
        public float $threeDartAverage,
        public int $count180s,
        public ?int $highestCheckout,
    ) {
    }
}
