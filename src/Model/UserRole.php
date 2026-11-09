<?php

declare(strict_types=1);

namespace App\Model;

enum UserRole: string
{
    case Player = 'PLAYER';
    case Organizer = 'ORGANIZER';
}
