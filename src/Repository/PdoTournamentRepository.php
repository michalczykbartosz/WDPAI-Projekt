<?php

declare(strict_types=1);

namespace App\Repository;

use App\Model\FinishRule;
use App\Model\GameMatch;
use App\Model\MatchStatus;
use App\Model\NewTournament;
use App\Model\Participant;
use App\Model\Tournament;
use App\Model\TournamentStatus;
use DateTimeImmutable;

final class PdoTournamentRepository extends PdoRepository implements TournamentRepositoryInterface
{
    private const SELECT_TOURNAMENT = <<<'SQL'
        SELECT t.*, count(tp.user_id) AS registered_players
        FROM tournaments t
        LEFT JOIN tournament_participants tp ON tp.tournament_id = t.id
        SQL;

    public function findPublished(?TournamentStatus $status = null, ?int $startingScore = null): array
    {
        $sql = self::SELECT_TOURNAMENT . " WHERE t.status <> 'draft'";
        $parameters = [];

        if ($status !== null) {
            $sql .= ' AND t.status = :status';
            $parameters['status'] = $status->value;
        }

        if ($startingScore !== null) {
            $sql .= ' AND t.starting_score = :starting_score';
            $parameters['starting_score'] = $startingScore;
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

    public function create(NewTournament $tournament, string $inviteCode): int
    {
        $row = $this->fetchOne(<<<'SQL'
            INSERT INTO tournaments (
                organizer_id, name, description, location, starts_at, registration_deadline,
                max_players, starting_score, finish_rule, legs_to_win, invite_code, status
            ) VALUES (
                :organizer_id, :name, :description, :location, :starts_at, :registration_deadline,
                :max_players, :starting_score, CAST(:finish_rule AS finish_rule), :legs_to_win, :invite_code, 'open'
            )
            RETURNING id
            SQL, [
            'organizer_id' => $tournament->organizerId,
            'name' => $tournament->name,
            'description' => $tournament->description,
            'location' => $tournament->location,
            'starts_at' => $tournament->startsAt->format(DATE_ATOM),
            'registration_deadline' => $tournament->registrationDeadline->format(DATE_ATOM),
            'max_players' => $tournament->maxPlayers,
            'starting_score' => $tournament->startingScore,
            'finish_rule' => $tournament->finishRule->value,
            'legs_to_win' => $tournament->legsToWin,
            'invite_code' => $inviteCode,
        ]);

        return (int) $row['id'];
    }

    public function inviteCodeExists(string $inviteCode): bool
    {
        $row = $this->fetchOne(
            'SELECT EXISTS (SELECT 1 FROM tournaments WHERE invite_code = :invite_code) AS taken',
            ['invite_code' => $inviteCode],
        );

        return (bool) $row['taken'];
    }

    public function isParticipant(int $tournamentId, int $userId): bool
    {
        $row = $this->fetchOne(<<<'SQL'
            SELECT EXISTS (
                SELECT 1 FROM tournament_participants
                WHERE tournament_id = :tournament_id AND user_id = :user_id
            ) AS registered
            SQL, ['tournament_id' => $tournamentId, 'user_id' => $userId]);

        return (bool) $row['registered'];
    }

    public function addParticipant(int $tournamentId, int $userId): AddParticipantResult
    {
        return $this->transaction(function () use ($tournamentId, $userId): AddParticipantResult {
            // FOR UPDATE blokuje wiersz turnieju do końca transakcji: drugi równoczesny zapis
            // czeka tutaj i liczy uczestników dopiero po zatwierdzeniu pierwszego.
            $tournament = $this->fetchOne(
                'SELECT max_players FROM tournaments WHERE id = :id FOR UPDATE',
                ['id' => $tournamentId],
            );

            $participants = $this->fetchOne(<<<'SQL'
                SELECT count(*) AS registered, coalesce(bool_or(user_id = :user_id), false) AS already_registered
                FROM tournament_participants
                WHERE tournament_id = :tournament_id
                SQL, ['tournament_id' => $tournamentId, 'user_id' => $userId]);

            if ($participants['already_registered']) {
                return AddParticipantResult::AlreadyRegistered;
            }

            if ((int) $participants['registered'] >= (int) $tournament['max_players']) {
                return AddParticipantResult::Full;
            }

            $this->execute(
                'INSERT INTO tournament_participants (tournament_id, user_id) VALUES (:tournament_id, :user_id)',
                ['tournament_id' => $tournamentId, 'user_id' => $userId],
            );

            return AddParticipantResult::Added;
        });
    }

    private function findOneBy(string $condition, int|string $value): ?Tournament
    {
        $row = $this->fetchOne(
            self::SELECT_TOURNAMENT . ' WHERE ' . $condition . ' GROUP BY t.id',
            ['value' => $value],
        );

        return $row === null ? null : $this->mapTournament($row);
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
}
