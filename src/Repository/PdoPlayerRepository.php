<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\MatchHistoryEntry;
use App\Model\PlayerProfile;
use App\Model\PlayerStats;
use DateTimeImmutable;

final class PdoPlayerRepository extends PdoRepository implements PlayerRepositoryInterface
{
    public function findLeaderboard(?int $limit = null): array
    {
        $sql = 'SELECT * FROM v_leaderboard ORDER BY rank, display_name';
        $parameters = [];

        if ($limit !== null) {
            $sql .= ' LIMIT :limit';
            $parameters['limit'] = $limit;
        }

        return array_map($this->mapStats(...), $this->fetchAll($sql, $parameters));
    }

    public function findStats(int $userId): ?PlayerStats
    {
        $row = $this->fetchOne('SELECT * FROM v_leaderboard WHERE user_id = :user_id', ['user_id' => $userId]);

        return $row === null ? null : $this->mapStats($row);
    }

    public function findProfile(int $userId): ?PlayerProfile
    {
        $row = $this->fetchOne(<<<'SQL'
            SELECT pp.user_id, pp.display_name, pp.club, pp.avatar_path, u.created_at
            FROM player_profiles pp
            JOIN users u ON u.id = pp.user_id
            WHERE pp.user_id = :user_id
            SQL, ['user_id' => $userId]);

        if ($row === null) {
            return null;
        }

        return new PlayerProfile(
            (int) $row['user_id'],
            $row['display_name'],
            $row['club'],
            $row['avatar_path'],
            new DateTimeImmutable($row['created_at']),
        );
    }

    public function findRecentMatches(int $userId, int $limit = 10): array
    {
        $rows = $this->fetchAll(<<<'SQL'
            WITH me AS (SELECT CAST(:user_id AS bigint) AS id)
            SELECT m.id AS match_id,
                   t.id AS tournament_id,
                   t.name AS tournament_name,
                   m.played_at,
                   side.opponent_id,
                   opp.display_name AS opponent_name,
                   side.player_legs,
                   side.opponent_legs,
                   m.winner_id = me.id AS won,
                   ms.three_dart_average,
                   ms.count_180s,
                   ms.highest_checkout
            FROM me
            JOIN matches m ON me.id IN (m.player1_id, m.player2_id)
            JOIN tournaments t ON t.id = m.tournament_id
            CROSS JOIN LATERAL (
                SELECT CASE WHEN m.player1_id = me.id THEN m.player2_id ELSE m.player1_id END AS opponent_id,
                       CASE WHEN m.player1_id = me.id THEN m.player1_legs ELSE m.player2_legs END AS player_legs,
                       CASE WHEN m.player1_id = me.id THEN m.player2_legs ELSE m.player1_legs END AS opponent_legs
            ) side
            LEFT JOIN player_profiles opp ON opp.user_id = side.opponent_id
            LEFT JOIN match_statistics ms ON ms.match_id = m.id AND ms.user_id = me.id
            WHERE m.status = 'finished'
            ORDER BY m.played_at DESC
            LIMIT :limit
            SQL, ['user_id' => $userId, 'limit' => $limit]);

        return array_map($this->mapHistoryEntry(...), $rows);
    }

    /** @param array<string, mixed> $row */
    private function mapStats(array $row): PlayerStats
    {
        return new PlayerStats(
            (int) $row['user_id'],
            $row['display_name'],
            $row['avatar_path'],
            (int) $row['rank'],
            (int) $row['tournaments_played'],
            (int) $row['matches_played'],
            (int) $row['wins'],
            (int) $row['losses'],
            (float) $row['win_rate'],
            self::nullableFloat($row['three_dart_average']),
            (int) $row['total_180s'],
            self::nullableInt($row['best_checkout']),
            (int) $row['tournaments_won'],
            (int) $row['points'],
        );
    }

    /** @param array<string, mixed> $row */
    private function mapHistoryEntry(array $row): MatchHistoryEntry
    {
        return new MatchHistoryEntry(
            (int) $row['match_id'],
            (int) $row['tournament_id'],
            $row['tournament_name'],
            new DateTimeImmutable($row['played_at']),
            self::nullableInt($row['opponent_id']),
            $row['opponent_name'],
            self::nullableInt($row['player_legs']),
            self::nullableInt($row['opponent_legs']),
            (bool) $row['won'],
            self::nullableFloat($row['three_dart_average']),
            self::nullableInt($row['count_180s']),
            self::nullableInt($row['highest_checkout']),
        );
    }
}
