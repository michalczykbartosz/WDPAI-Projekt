<?php

declare(strict_types=1);

namespace App\Controller;

final class DefaultController extends AppController
{
    public function index(): void
    {
        $upcomingTournaments = [
            ['id' => 1, 'name' => 'Puchar Krakowa 2026', 'date' => '2026-11-15', 'status' => 'Zapisy otwarte'],
            ['id' => 2, 'name' => 'Liga Zimowa – Runda 1', 'date' => '2026-12-01', 'status' => 'Zapisy otwarte'],
        ];

        $topPlayers = [
            ['rank' => 1, 'name' => 'Piotr Wiśniewski', 'avg3dart' => 91.0],
            ['rank' => 2, 'name' => 'Tomasz Lewandowski', 'avg3dart' => 88.1],
            ['rank' => 3, 'name' => 'Jan Kowalski', 'avg3dart' => 85.4],
        ];

        $this->render('home', [
            'pageTitle' => 'DartTournament – Platforma turniejów darta',
            'upcomingTournaments' => $upcomingTournaments,
            'topPlayers' => $topPlayers,
        ]);
    }

    public function notFound(): void
    {
        $this->render('not-found', [
            'pageTitle' => '404 – Nie znaleziono strony – DartTournament',
            'suggestedLinks' => [
                ['url' => '/', 'label' => 'Strona główna'],
                ['url' => '/tournaments', 'label' => 'Turnieje'],
                ['url' => '/leaderboard', 'label' => 'Ranking'],
            ],
        ]);
    }
}
