<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Tournament;
use App\Model\TournamentStatus;
use App\Repository\AddParticipantResult;
use App\Repository\TournamentRepositoryInterface;
use DateTimeImmutable;

final class TournamentRegistrationService
{
    public function __construct(private readonly TournamentRepositoryInterface $tournaments)
    {
    }

    /**
     * Sprawdza, czy gracz może się zapisać. Używane przy zapisie i przy wyświetlaniu
     * strony turnieju, żeby przycisk od razu pokazywał właściwy stan.
     */
    public function check(Tournament $tournament, bool $alreadyRegistered, DateTimeImmutable $now): ?TournamentRegistrationError
    {
        if ($alreadyRegistered) {
            return TournamentRegistrationError::AlreadyRegistered;
        }

        if (!$tournament->isRegistrationOpen($now)) {
            return TournamentRegistrationError::Closed;
        }

        if ($tournament->registeredPlayers >= $tournament->maxPlayers) {
            return TournamentRegistrationError::Full;
        }

        return null;
    }

    /**
     * Zapisuje gracza na turniej i zwraca turniej z aktualną liczbą zapisanych.
     *
     * @throws TournamentRegistrationException gdy zapis jest niemożliwy
     */
    public function register(int $tournamentId, int $userId): Tournament
    {
        $tournament = $this->findVisible($tournamentId);

        $error = $this->check(
            $tournament,
            $this->tournaments->isParticipant($tournamentId, $userId),
            new DateTimeImmutable(),
        );

        if ($error !== null) {
            throw new TournamentRegistrationException($error);
        }

        // Limit miejsc i duplikat sprawdza jeszcze raz repozytorium w transakcji z blokadą,
        // bo między sprawdzeniem powyżej a zapisem mógł się zapisać ktoś inny.
        $result = $this->tournaments->addParticipant($tournamentId, $userId);

        match ($result) {
            AddParticipantResult::Added => null,
            AddParticipantResult::AlreadyRegistered => throw new TournamentRegistrationException(TournamentRegistrationError::AlreadyRegistered),
            AddParticipantResult::Full => throw new TournamentRegistrationException(TournamentRegistrationError::Full),
        };

        return $this->findVisible($tournamentId);
    }

    private function findVisible(int $tournamentId): Tournament
    {
        $tournament = $this->tournaments->findById($tournamentId);

        if ($tournament === null || $tournament->status === TournamentStatus::Draft) {
            throw new TournamentRegistrationException(TournamentRegistrationError::NotFound);
        }

        return $tournament;
    }
}
