<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\GameMatch;
use App\Model\MatchResult;
use App\Model\MatchStatus;
use App\Model\PlayerMatchStats;
use App\Model\Tournament;
use App\Model\TournamentStatus;
use App\Model\UserRole;
use App\Repository\SaveMatchResultOutcome;
use App\Repository\TournamentRepositoryInterface;

final class MatchResultService
{
    public function __construct(
        private readonly MatchScoreValidator $validator,
        private readonly TournamentRepositoryInterface $tournaments,
    ) {
    }

    /** Czy pokazać temu użytkownikowi przycisk wpisania wyniku przy meczu. */
    public function canSubmit(GameMatch $match, Tournament $tournament, ?int $userId, ?UserRole $role): bool
    {
        return $userId !== null
            && $role !== null
            && $this->isPlayable($match, $tournament)
            && $this->mayEnterResult($match, $tournament, $userId, $role);
    }

    /**
     * Zapisuje wynik meczu. Zwraca true, gdy był to finał i turniej się zakończył.
     *
     * @param array<string, string> $input pola z MatchScoreValidator::FIELDS
     * @throws MatchResultException gdy meczu nie ma, użytkownik nie może go wpisać albo mecz nie czeka na wynik
     * @throws ValidationException gdy wynik jest niepoprawny
     */
    public function submit(int $matchId, int $userId, UserRole $role, array $input): bool
    {
        $match = $this->tournaments->findMatch($matchId);
        $tournament = $match !== null ? $this->tournaments->findById($match->tournamentId) : null;

        if ($match === null || $tournament === null || $tournament->status === TournamentStatus::Draft) {
            throw new MatchResultException(MatchResultError::NotFound);
        }

        if (!$this->mayEnterResult($match, $tournament, $userId, $role)) {
            throw new MatchResultException(MatchResultError::Forbidden);
        }

        if (!$this->isPlayable($match, $tournament)) {
            throw new MatchResultException(MatchResultError::NotPlayable);
        }

        $errors = $this->validator->validate($input, $tournament->legsToWin, $tournament->finishRule);

        if ($errors !== []) {
            throw new ValidationException($errors, ['legs' => $tournament->legsToWin]);
        }

        $outcome = $this->tournaments->saveMatchResult($matchId, $this->toResult($input, $tournament->legsToWin));

        return match ($outcome) {
            SaveMatchResultOutcome::Saved => false,
            SaveMatchResultOutcome::TournamentFinished => true,
            SaveMatchResultOutcome::NotPlayable => throw new MatchResultException(MatchResultError::NotPlayable),
        };
    }

    /**
     * Wynik wpisuje organizator tego turnieju albo jeden z dwóch graczy tego meczu.
     * Inny gracz (także z tego samego turnieju) dostaje 403.
     */
    private function mayEnterResult(GameMatch $match, Tournament $tournament, int $userId, UserRole $role): bool
    {
        return match ($role) {
            UserRole::Organizer => $tournament->isOwnedBy($userId),
            UserRole::Player => $match->hasPlayer($userId),
        };
    }

    /** Mecz czeka na wynik: turniej trwa, znamy obu graczy i nie ma jeszcze wyniku. */
    private function isPlayable(GameMatch $match, Tournament $tournament): bool
    {
        return $tournament->status === TournamentStatus::Ongoing
            && $match->status === MatchStatus::Pending
            && $match->player1Id !== null
            && $match->player2Id !== null;
    }

    /** @param array<string, string> $input dane już sprawdzone przez walidator */
    private function toResult(array $input, int $legsToWin): MatchResult
    {
        return new MatchResult(
            MatchScoreValidator::parseLegs($input['score1'], $legsToWin),
            MatchScoreValidator::parseLegs($input['score2'], $legsToWin),
            $this->toStats($input, 1),
            $this->toStats($input, 2),
        );
    }

    /** @param array<string, string> $input */
    private function toStats(array $input, int $player): PlayerMatchStats
    {
        $checkout = $input['checkout' . $player];

        return new PlayerMatchStats(
            MatchScoreValidator::parseAverage($input['avg' . $player]),
            MatchScoreValidator::parse180s($input['max180_' . $player]),
            $checkout !== '' ? (int) $checkout : null,
        );
    }
}
