<?php

declare(strict_types=1);

namespace App\Controller;

use App\Model\TournamentStatus;
use App\Repository\PlayerRepositoryInterface;
use App\Repository\TournamentRepositoryInterface;

final class DefaultController extends AppController
{
    public function __construct(
        private readonly TournamentRepositoryInterface $tournaments,
        private readonly PlayerRepositoryInterface $players,
    ) {
        parent::__construct();
    }

    public function index(): void
    {
        $this->render('home', [
            'pageTitle' => $this->translator->get('title.home'),
            'upcomingTournaments' => $this->tournaments->findPublished(TournamentStatus::Open),
            'topPlayers' => $this->players->findLeaderboard(3),
        ]);
    }
}
