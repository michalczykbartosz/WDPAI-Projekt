<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\GameMatch;
use App\Model\TournamentStatus;
use App\Repository\TournamentRepositoryInterface;

final class TournamentController extends AppController
{
    public function __construct(private readonly TournamentRepositoryInterface $tournaments)
    {
        parent::__construct();
    }

    public function index(): void
    {
        $this->render('tournaments', [
            'pageTitle' => $this->translator->get('title.tournaments'),
            'activePage' => 'tournaments',
            'tournaments' => $this->tournaments->findPublished(),
        ]);
    }

    public function show(string $id): void
    {
        $tournamentId = $this->parseId($id);

        if ($tournamentId === null) {
            $this->badRequest();
            return;
        }

        $tournament = $this->tournaments->findById($tournamentId);

        if ($tournament === null || $tournament->status === TournamentStatus::Draft) {
            $this->notFound();
            return;
        }

        $this->render('tournament-detail', [
            'pageTitle' => $this->translator->get('title.tournament', ['name' => $tournament->name]),
            'activePage' => 'tournaments',
            'tournament' => $tournament,
            'players' => $this->tournaments->findParticipants($tournamentId),
            'rounds' => $this->groupByRound($this->tournaments->findMatches($tournamentId), $tournament->maxPlayers),
        ]);
    }

    public function create(): void
    {
        $this->render('organizer-create', [
            'pageTitle' => $this->translator->get('title.organizer_create'),
            'activePage' => 'organizer',
            'userRole' => 'organizer',
            'formats' => ['501 Double Out', '301 Double Out', '501 Single Out'],
            'maxPlayersOptions' => [4, 8, 16],
        ]);
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
