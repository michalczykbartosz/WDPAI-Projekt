<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\Bracket;
use App\Model\GameMatch;
use App\Model\NewTournament;
use App\Model\Participant;
use App\Model\Tournament;
use App\Model\TournamentStatus;

interface TournamentRepositoryInterface
{
    /**
     * @param string|null $search fragment nazwy albo lokalizacji (bez rozróżniania wielkości liter)
     * @return list<Tournament>
     */
    public function findPublished(?TournamentStatus $status = null, ?int $startingScore = null, ?string $search = null): array;

    public function findById(int $id): ?Tournament;

    public function findByInviteCode(string $inviteCode): ?Tournament;

    /** @return list<Participant> */
    public function findParticipants(int $tournamentId): array;

    /** @return list<GameMatch> */
    public function findMatches(int $tournamentId): array;

    /** Zapisuje turniej otwarty na zapisy i zwraca jego identyfikator. */
    public function create(NewTournament $tournament, string $inviteCode): int;

    public function inviteCodeExists(string $inviteCode): bool;

    public function isParticipant(int $tournamentId, int $userId): bool;

    /** Dopisuje gracza do turnieju, pilnując limitu miejsc także przy równoczesnych zapisach. */
    public function addParticipant(int $tournamentId, int $userId): AddParticipantResult;

    /**
     * Zapisuje mecze drabinki i rozstawienie graczy, a turniej przechodzi w status ongoing.
     * Zwraca false, gdy turniej nie jest już otwarty (np. drabinka powstała w międzyczasie).
     */
    public function saveBracket(int $tournamentId, Bracket $bracket): bool;
}
