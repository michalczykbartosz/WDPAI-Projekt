<?php

declare(strict_types=1);

namespace App\Model;

enum TournamentStatus: string
{
    case Draft = 'draft';
    case Open = 'open';
    case Ongoing = 'ongoing';
    case Finished = 'finished';
}
