<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\TournamentStatus;
use App\Repository\TournamentRepositoryInterface;

final class DefaultController extends AppController
{
    public function __construct(private readonly TournamentRepositoryInterface $tournaments)
    {
        parent::__construct();
    }

    public function index(): void
    {
        $topPlayers = [
            ['rank' => 1, 'name' => 'Piotr Wiśniewski', 'avg3dart' => 91.0],
            ['rank' => 2, 'name' => 'Tomasz Lewandowski', 'avg3dart' => 88.1],
            ['rank' => 3, 'name' => 'Jan Kowalski', 'avg3dart' => 85.4],
        ];

        $this->render('home', [
            'pageTitle' => $this->translator->get('title.home'),
            'upcomingTournaments' => $this->tournaments->findPublished(TournamentStatus::Open),
            'topPlayers' => $topPlayers,
        ]);
    }
}
