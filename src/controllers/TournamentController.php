<?php

declare(strict_types=1);

namespace App\Controller;

final class TournamentController extends AppController
{
    public function index(): void
    {
        $tournaments = [
            [
                'id' => 1,
                'name' => 'Puchar Krakowa 2026',
                'format' => '501 Double Out',
                'date' => '2026-11-15',
                'status' => 'Zapisy otwarte',
                'maxPlayers' => 16,
                'registeredPlayers' => 9,
                'location' => 'Kraków, Dart Arena',
            ],
            [
                'id' => 2,
                'name' => 'Liga Zimowa – Runda 1',
                'format' => '501 Double Out',
                'date' => '2026-12-01',
                'status' => 'Zapisy otwarte',
                'maxPlayers' => 8,
                'registeredPlayers' => 5,
                'location' => 'Warszawa, Bull\'s Eye Pub',
            ],
            [
                'id' => 3,
                'name' => 'Turniej Mikołajkowy',
                'format' => '301 Double Out',
                'date' => '2026-12-06',
                'status' => 'Zakończony',
                'maxPlayers' => 8,
                'registeredPlayers' => 8,
                'location' => 'Gdańsk, Triple Twenty Club',
            ],
        ];

        $this->render('tournaments', [
            'pageTitle' => 'Turnieje – DartArena',
            'activePage' => 'tournaments',
            'tournaments' => $tournaments,
        ]);
    }

    public function show(int $id): void
    {
        $tournament = [
            'id' => $id,
            'name' => 'Puchar Krakowa 2026',
            'format' => '501 Double Out',
            'date' => '2026-11-15',
            'status' => 'W trakcie',
            'location' => 'Kraków, Dart Arena',
            'maxPlayers' => 8,
        ];

        $players = [
            ['seed' => 1, 'name' => 'Jan Kowalski', 'avg' => 85.4],
            ['seed' => 2, 'name' => 'Adam Nowak', 'avg' => 78.2],
            ['seed' => 3, 'name' => 'Piotr Wiśniewski', 'avg' => 91.0],
            ['seed' => 4, 'name' => 'Krzysztof Zieliński', 'avg' => 72.6],
            ['seed' => 5, 'name' => 'Tomasz Lewandowski', 'avg' => 88.1],
            ['seed' => 6, 'name' => 'Marcin Wójcik', 'avg' => 69.3],
            ['seed' => 7, 'name' => 'Paweł Kamiński', 'avg' => 80.5],
            ['seed' => 8, 'name' => 'Łukasz Szymański', 'avg' => 74.9],
        ];

        $bracket = [
            'quarterfinals' => [
                ['id' => 101, 'player1' => 'Jan Kowalski', 'player2' => 'Łukasz Szymański', 'score1' => 3, 'score2' => 1, 'status' => 'Zakończony'],
                ['id' => 102, 'player1' => 'Piotr Wiśniewski', 'player2' => 'Marcin Wójcik', 'score1' => 3, 'score2' => 0, 'status' => 'Zakończony'],
                ['id' => 103, 'player1' => 'Tomasz Lewandowski', 'player2' => 'Krzysztof Zieliński', 'score1' => 2, 'score2' => 3, 'status' => 'Zakończony'],
                ['id' => 104, 'player1' => 'Adam Nowak', 'player2' => 'Paweł Kamiński', 'score1' => null, 'score2' => null, 'status' => 'Oczekuje'],
            ],
            'semifinals' => [
                ['id' => 201, 'player1' => 'Jan Kowalski', 'player2' => 'Piotr Wiśniewski', 'score1' => null, 'score2' => null, 'status' => 'Oczekuje'],
                ['id' => 202, 'player1' => 'Krzysztof Zieliński', 'player2' => '—', 'score1' => null, 'score2' => null, 'status' => 'Oczekuje'],
            ],
            'final' => [
                ['id' => 301, 'player1' => '—', 'player2' => '—', 'score1' => null, 'score2' => null, 'status' => 'Oczekuje'],
            ],
        ];

        $this->render('tournament-detail', [
            'pageTitle' => $tournament['name'] . ' – DartArena',
            'activePage' => 'tournaments',
            'tournament' => $tournament,
            'players' => $players,
            'bracket' => $bracket,
        ]);
    }

    public function create(): void
    {
        $this->render('organizer-create', [
            'pageTitle' => 'Utwórz turniej – Panel Organizatora',
            'activePage' => 'organizer',
            'userRole' => 'organizer',
            'formats' => ['501 Double Out', '301 Double Out', '501 Single Out'],
            'maxPlayersOptions' => [4, 8, 16],
        ]);
    }
}
