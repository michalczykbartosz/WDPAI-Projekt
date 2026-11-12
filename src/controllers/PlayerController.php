<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\PlayerRepositoryInterface;

final class PlayerController extends AppController
{
    public function __construct(private readonly PlayerRepositoryInterface $players)
    {
        parent::__construct();
    }

    public function leaderboard(): void
    {
        $this->render('leaderboard', [
            'pageTitle' => $this->translator->get('title.leaderboard'),
            'activePage' => 'leaderboard',
            'players' => $this->players->findLeaderboard(),
        ]);
    }

    public function profile(int $id): void
    {
        $profile = $this->players->findProfile($id);
        $stats = $this->players->findStats($id);

        if ($profile === null || $stats === null) {
            $this->notFound();
            return;
        }

        $this->render('player-profile', [
            'pageTitle' => $this->translator->get('title.profile', ['name' => $profile->displayName]),
            'profile' => $profile,
            'stats' => $stats,
            'recentMatches' => $this->players->findRecentMatches($id),
        ]);
    }
}
