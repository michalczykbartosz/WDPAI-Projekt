<?php

declare(strict_types=1);

use App\Controller\DefaultController;
use App\Controller\PlayerController;
use App\Controller\TournamentController;
use App\Routing;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$routing = new Routing();

// Strona główna
$routing->get('/', DefaultController::class, 'index');

// Turnieje – lista, szczegóły z parametrem {id}, tworzenie
$routing->get('/tournaments', TournamentController::class, 'index');
$routing->get('/tournaments/{id}', TournamentController::class, 'show');
$routing->get('/organizer/create', TournamentController::class, 'create');

// Gracze – ranking globalny, profil z parametrem {id}
$routing->get('/leaderboard', PlayerController::class, 'leaderboard');
$routing->get('/players/{id}', PlayerController::class, 'profile');

$routeFound = $routing->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);

if (!$routeFound) {
    http_response_code(404);
    (new DefaultController())->notFound();
}
