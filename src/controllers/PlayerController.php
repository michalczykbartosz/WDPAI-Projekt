<?php

declare(strict_types=1);

namespace App\Controller;

final class PlayerController extends AppController
{
    public function leaderboard(): void
    {
        $players = [
            ['rank' => 1, 'name' => 'Piotr Wiśniewski', 'matchesPlayed' => 34, 'wins' => 28, 'winRate' => 82.4, 'avg3dart' => 91.0, 'max180s' => 47, 'bestCheckout' => 164],
            ['rank' => 2, 'name' => 'Tomasz Lewandowski', 'matchesPlayed' => 30, 'wins' => 24, 'winRate' => 80.0, 'avg3dart' => 88.1, 'max180s' => 39, 'bestCheckout' => 170],
            ['rank' => 3, 'name' => 'Jan Kowalski', 'matchesPlayed' => 29, 'wins' => 22, 'winRate' => 75.9, 'avg3dart' => 85.4, 'max180s' => 35, 'bestCheckout' => 156],
            ['rank' => 4, 'name' => 'Paweł Kamiński', 'matchesPlayed' => 25, 'wins' => 18, 'winRate' => 72.0, 'avg3dart' => 80.5, 'max180s' => 28, 'bestCheckout' => 148],
            ['rank' => 5, 'name' => 'Adam Nowak', 'matchesPlayed' => 22, 'wins' => 15, 'winRate' => 68.2, 'avg3dart' => 78.2, 'max180s' => 22, 'bestCheckout' => 140],
            ['rank' => 6, 'name' => 'Łukasz Szymański', 'matchesPlayed' => 20, 'wins' => 13, 'winRate' => 65.0, 'avg3dart' => 74.9, 'max180s' => 18, 'bestCheckout' => 132],
            ['rank' => 7, 'name' => 'Krzysztof Zieliński', 'matchesPlayed' => 18, 'wins' => 11, 'winRate' => 61.1, 'avg3dart' => 72.6, 'max180s' => 14, 'bestCheckout' => 120],
            ['rank' => 8, 'name' => 'Marcin Wójcik', 'matchesPlayed' => 15, 'wins' => 8, 'winRate' => 53.3, 'avg3dart' => 69.3, 'max180s' => 10, 'bestCheckout' => 108],
        ];

        $this->render('leaderboard', [
            'pageTitle' => 'Ranking graczy – DartTournament',
            'activePage' => 'leaderboard',
            'players' => $players,
        ]);
    }

    public function profile(int $id): void
    {
        $player = [
            'id' => $id,
            'name' => 'Jan Kowalski',
            'avatar' => null,
            'joinedDate' => '2025-03-10',
            'matchesPlayed' => 29,
            'wins' => 22,
            'losses' => 7,
            'winRate' => 75.9,
            'avg3dart' => 85.4,
            'total180s' => 35,
            'bestCheckout' => 156,
        ];

        $recentMatches = [
            ['date' => '2026-10-28', 'tournament' => 'Puchar Krakowa 2026', 'opponent' => 'Łukasz Szymański', 'result' => '3:1', 'avg' => 87.2, 'max180s' => 2, 'checkout' => 96],
            ['date' => '2026-10-20', 'tournament' => 'Liga Jesienna – Runda 4', 'opponent' => 'Marcin Wójcik', 'result' => '3:0', 'avg' => 92.1, 'max180s' => 3, 'checkout' => 156],
            ['date' => '2026-10-12', 'tournament' => 'Liga Jesienna – Runda 3', 'opponent' => 'Piotr Wiśniewski', 'result' => '1:3', 'avg' => 80.5, 'max180s' => 1, 'checkout' => 64],
            ['date' => '2026-10-05', 'tournament' => 'Liga Jesienna – Runda 2', 'opponent' => 'Adam Nowak', 'result' => '3:2', 'avg' => 83.7, 'max180s' => 2, 'checkout' => 120],
        ];

        $this->render('player-profile', [
            'pageTitle' => $player['name'] . ' – Profil – DartTournament',
            'player' => $player,
            'recentMatches' => $recentMatches,
        ]);
    }
}
