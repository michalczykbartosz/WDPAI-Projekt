<?php

declare(strict_types=1);

namespace App\Model;

enum FinishRule: string
{
    case DoubleOut = 'double_out';
    case SingleOut = 'single_out';

    public function label(): string
    {
        return match ($this) {
            self::DoubleOut => 'Double Out',
            self::SingleOut => 'Single Out',
        };
    }
}
