<?php

declare(strict_types=1);

namespace App\Repository;

enum SaveMatchResultOutcome
{
    /** Wynik zapisany, zwycięzca przeszedł do następnej rundy. */
    case Saved;
    /** Wynik finału zapisany – turniej jest zakończony. */
    case TournamentFinished;
    /** Mecz został w międzyczasie rozegrany albo nie ma jeszcze obu graczy. */
    case NotPlayable;
}
