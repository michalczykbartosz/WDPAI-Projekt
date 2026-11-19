<?php

declare(strict_types=1);

namespace App\Security;

/**
 * Wynik sprawdzenia uprawnień: Unauthenticated oznacza brak logowania (401),
 * Forbidden – zalogowanego użytkownika z niewłaściwą rolą (403).
 */
enum AccessDecision
{
    case Granted;
    case Unauthenticated;
    case Forbidden;
}
