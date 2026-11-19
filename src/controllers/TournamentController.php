<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\GameMatch;
use App\Model\Tournament;
use App\Model\TournamentStatus;
use App\Model\UserRole;
use App\Repository\TournamentRepositoryInterface;

final class TournamentController extends AppController
{
    public function __construct(private readonly TournamentRepositoryInterface $tournaments)
    {
        parent::__construct();
    }

    private const PUBLIC_STATUSES = [TournamentStatus::Open, TournamentStatus::Ongoing, TournamentStatus::Finished];

    public function index(): void
    {
        $status = $this->parseStatusFilter($_GET['status'] ?? '');
        $startingScore = $this->parseFormatFilter($_GET['format'] ?? '');

        if ($status === false || $startingScore === false) {
            $this->badRequest();
            return;
        }

        $this->render('tournaments', [
            'pageTitle' => $this->translator->get('title.tournaments'),
            'activePage' => 'tournaments',
            'tournaments' => $this->tournaments->findPublished($status, $startingScore),
            'statusOptions' => self::PUBLIC_STATUSES,
            'formatOptions' => Tournament::STARTING_SCORES,
            'selectedStatus' => $status?->value ?? '',
            'selectedFormat' => $startingScore !== null ? (string) $startingScore : '',
        ]);
    }

    public function show(string $id): void
    {
        $tournamentId = $this->parseId($id);

        if ($tournamentId === null) {
            $this->badRequest();
            return;
        }

        $this->renderTournament($this->tournaments->findById($tournamentId));
    }

    public function showByCode(string $code): void
    {
        $this->renderTournament($this->tournaments->findByInviteCode($code));
    }

    public function create(): void
    {
        if ($this->denyUnlessRole(UserRole::Organizer)) {
            return;
        }

        $this->render('organizer-create', [
            'pageTitle' => $this->translator->get('title.organizer_create'),
            'activePage' => 'organizer',
            'formats' => ['501 Double Out', '301 Double Out', '501 Single Out'],
            'maxPlayersOptions' => [4, 8, 16],
        ]);
    }

    private function renderTournament(?Tournament $tournament): void
    {
        if ($tournament === null || $tournament->status === TournamentStatus::Draft) {
            $this->notFound();
            return;
        }

        $this->render('tournament-detail', [
            'pageTitle' => $this->translator->get('title.tournament', ['name' => $tournament->name]),
            'activePage' => 'tournaments',
            'tournament' => $tournament,
            'players' => $this->tournaments->findParticipants($tournament->id),
            'rounds' => $this->groupByRound($this->tournaments->findMatches($tournament->id), $tournament->maxPlayers),
        ]);
    }

    /**
     * Pusty parametr oznacza brak filtra (null), nieprawidłowa wartość to false.
     */
    private function parseStatusFilter(mixed $value): TournamentStatus|null|false
    {
        if ($value === '') {
            return null;
        }

        $status = is_string($value) ? TournamentStatus::tryFrom($value) : null;

        return in_array($status, self::PUBLIC_STATUSES, true) ? $status : false;
    }

    private function parseFormatFilter(mixed $value): int|null|false
    {
        if ($value === '') {
            return null;
        }

        $startingScore = is_string($value) ? $this->parseId($value) : null;

        return in_array($startingScore, Tournament::STARTING_SCORES, true) ? $startingScore : false;
    }

    /**
     * @param list<GameMatch> $matches
     * @return list<array{number: int, label: string, matches: list<GameMatch>}>
     */
    private function groupByRound(array $matches, int $maxPlayers): array
    {
        $totalRounds = (int) log($maxPlayers, 2);
        $rounds = [];

        foreach ($matches as $match) {
            $rounds[$match->round] ??= [
                'number' => $match->round,
                'label' => $this->roundLabel($match->round, $totalRounds),
                'matches' => [],
            ];
            $rounds[$match->round]['matches'][] = $match;
        }

        return array_values($rounds);
    }

    private function roundLabel(int $round, int $totalRounds): string
    {
        return match ($totalRounds - $round) {
            0 => $this->translator->get('bracket.round.final'),
            1 => $this->translator->get('bracket.round.semifinals'),
            2 => $this->translator->get('bracket.round.quarterfinals'),
            default => $this->translator->get('bracket.round.number', ['number' => $round]),
        };
    }
}
