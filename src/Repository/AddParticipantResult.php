<?php

declare(strict_types=1);

namespace App\Repository;

enum AddParticipantResult
{
    case Added;
    case AlreadyRegistered;
    case Full;
}
