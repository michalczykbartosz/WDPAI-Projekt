<?php

declare(strict_types=1);

namespace App\Repository;

use App\Database\Database;
use App\Model\FinishRule;
use App\Model\GameMatch;
use App\Model\MatchStatus;
use App\Model\Participant;
use App\Model\Tournament;
use App\Model\TournamentStatus;
use DateTimeImmutable;

final class PdoTournamentRepository implements TournamentRepositoryInterface
{
    private const SELECT_TOURNAMENT = <<<'SQL'
        SELECT t.*, count(tp.user_id) AS registered_players
        FROM tournaments t
        LEFT JOIN tournament_participants tp ON tp.tournament_id = t.id
        SQL;

    public function __construct(private readonly Database $database)
    {
    }

    public function findPublished(?TournamentStatus $status = null): array
    {
        $sql = self::SELECT_TOURNAMENT . " WHERE t.status <> 'draft'";
        $parameters = [];

        if ($status !== null) {
            $sql .= ' AND t.status = :status';
            $parameters['status'] = $status->value;
        }

        $sql .= ' GROUP BY t.id ORDER BY t.starts_at';

        return array_map($this->mapTournament(...), $this->fetchAll($sql, $parameters));
    }

    public function findById(int $id): ?Tournament
    {
        return $this->findOneBy('t.id = :value', $id);
    }

    public function findByInviteCode(string $inviteCode): ?Tournament
    {
        return $this->findOneBy('t.invite_code = :value', $inviteCode);
    }

    public function findParticipants(int $tournamentId): array
    {
        $rows = $this->fetchAll(<<<'SQL'
            SELECT tp.user_id, pp.display_name, tp.seed, lb.three_dart_average
            FROM tournament_participants tp
            JOIN player_profiles pp ON pp.user_id = tp.user_id
            LEFT JOIN v_leaderboard lb ON lb.user_id = tp.user_id
            WHERE tp.tournament_id = :tournament_id
            ORDER BY tp.seed NULLS LAST, tp.registered_at
            SQL, ['tournament_id' => $tournamentId]);

        return array_map($this->mapParticipant(...), $rows);
    }

    public function findMatches(int $tournamentId): array
    {
        $rows = $this->fetchAll(<<<'SQL'
            SELECT m.*, p1.display_name AS player1_name, p2.display_name AS player2_name
            FROM matches m
            LEFT JOIN player_profiles p1 ON p1.user_id = m.player1_id
            LEFT JOIN player_profiles p2 ON p2.user_id = m.player2_id
            WHERE m.tournament_id = :tournament_id
            ORDER BY m.round, m.position
            SQL, ['tournament_id' => $tournamentId]);

        return array_map($this->mapMatch(...), $rows);
    }

    private function findOneBy(string $condition, int|string $value): ?Tournament
    {
        $rows = $this->fetchAll(
            self::SELECT_TOURNAMENT . ' WHERE ' . $condition . ' GROUP BY t.id',
            ['value' => $value],
        );

        return $rows === [] ? null : $this->mapTournament($rows[0]);
    }

    /**
     * @param array<string, int|string> $parameters
     * @return list<array<string, mixed>>
     */
    private function fetchAll(string $sql, array $parameters): array
    {
        $statement = $this->database->connection()->prepare($sql);
        $statement->execute($parameters);

        return $statement->fetchAll();
    }

    /** @param array<string, mixed> $row */
    private function mapTournament(array $row): Tournament
    {
        return new Tournament(
            (int) $row['id'],
            (int) $row['organizer_id'],
            $row['name'],
            $row['description'],
            $row['location'],
            new DateTimeImmutable($row['starts_at']),
            new DateTimeImmutable($row['registration_deadline']),
            (int) $row['max_players'],
            (int) $row['starting_score'],
            FinishRule::from($row['finish_rule']),
            (int) $row['legs_to_win'],
            $row['invite_code'],
            TournamentStatus::from($row['status']),
            (int) $row['registered_players'],
        );
    }

    /** @param array<string, mixed> $row */
    private function mapParticipant(array $row): Participant
    {
        return new Participant(
            (int) $row['user_id'],
            $row['display_name'],
            self::nullableInt($row['seed']),
            $row['three_dart_average'] !== null ? (float) $row['three_dart_average'] : null,
        );
    }

    /** @param array<string, mixed> $row */
    private function mapMatch(array $row): GameMatch
    {
        return new GameMatch(
            (int) $row['id'],
            (int) $row['tournament_id'],
            (int) $row['round'],
            (int) $row['position'],
            self::nullableInt($row['player1_id']),
            $row['player1_name'],
            self::nullableInt($row['player2_id']),
            $row['player2_name'],
            self::nullableInt($row['player1_legs']),
            self::nullableInt($row['player2_legs']),
            self::nullableInt($row['winner_id']),
            MatchStatus::from($row['status']),
            $row['played_at'] !== null ? new DateTimeImmutable($row['played_at']) : null,
        );
    }

    private static function nullableInt(mixed $value): ?int
    {
        return $value !== null ? (int) $value : null;
    }
}
