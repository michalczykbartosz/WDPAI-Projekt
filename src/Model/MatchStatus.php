<?php

declare(strict_types=1);

namespace App\Model;

enum MatchStatus: string
{
    case Pending = 'pending';
    case Finished = 'finished';
}
