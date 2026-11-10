<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\GameMatch;
use App\Model\Participant;
use App\Model\Tournament;
use App\Model\TournamentStatus;

interface TournamentRepositoryInterface
{
    /** @return list<Tournament> */
    public function findPublished(?TournamentStatus $status = null): array;

    public function findById(int $id): ?Tournament;

    public function findByInviteCode(string $inviteCode): ?Tournament;

    /** @return list<Participant> */
    public function findParticipants(int $tournamentId): array;

    /** @return list<GameMatch> */
    public function findMatches(int $tournamentId): array;
}
