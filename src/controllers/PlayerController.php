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

    public function profile(string $id): void
    {
        $playerId = $this->parseId($id);

        if ($playerId === null) {
            $this->badRequest();
            return;
        }

        $profile = $this->players->findProfile($playerId);
        $stats = $this->players->findStats($playerId);

        if ($profile === null || $stats === null) {
            $this->notFound();
            return;
        }

        $this->render('player-profile', [
            'pageTitle' => $this->translator->get('title.profile', ['name' => $profile->displayName]),
            'profile' => $profile,
            'stats' => $stats,
            'recentMatches' => $this->players->findRecentMatches($playerId),
        ]);
    }
}
