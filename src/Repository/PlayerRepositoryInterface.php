<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\MatchHistoryEntry;
use App\Model\PlayerProfile;
use App\Model\PlayerStats;

interface PlayerRepositoryInterface
{
    /** @return list<PlayerStats> */
    public function findLeaderboard(?int $limit = null): array;

    public function findStats(int $userId): ?PlayerStats;

    public function findProfile(int $userId): ?PlayerProfile;

    /** @return list<MatchHistoryEntry> */
    public function findRecentMatches(int $userId, int $limit = 10): array;
}
