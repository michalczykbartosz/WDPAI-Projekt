<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\GameMatch;
use App\Model\NewTournament;
use App\Model\Participant;
use App\Model\Tournament;
use App\Model\TournamentStatus;

interface TournamentRepositoryInterface
{
    /** @return list<Tournament> */
    public function findPublished(?TournamentStatus $status = null, ?int $startingScore = null): array;

    public function findById(int $id): ?Tournament;

    public function findByInviteCode(string $inviteCode): ?Tournament;

    /** @return list<Participant> */
    public function findParticipants(int $tournamentId): array;

    /** @return list<GameMatch> */
    public function findMatches(int $tournamentId): array;

    /** Zapisuje turniej otwarty na zapisy i zwraca jego identyfikator. */
    public function create(NewTournament $tournament, string $inviteCode): int;

    public function inviteCodeExists(string $inviteCode): bool;
}
