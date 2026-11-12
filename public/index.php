<?php

declare(strict_types=1);

use App\Controller\DefaultController;
use App\Controller\LanguageController;
use App\Controller\PlayerController;
use App\Controller\TournamentController;
use App\Container;
use App\Routing;

require_once dirname(__DIR__) . '/vendor/autoload.php';

session_start([
    'cookie_httponly' => true,
    'cookie_samesite' => 'Lax',
    'use_strict_mode' => true,
]);

$container = new Container();
$routing = new Routing($container);

// Strona główna
$routing->get('/', DefaultController::class, 'index');

// Turnieje – lista, szczegóły z parametrem {id}, tworzenie
$routing->get('/tournaments', TournamentController::class, 'index');
$routing->get('/tournaments/{id}', TournamentController::class, 'show');
$routing->get('/organizer/create', TournamentController::class, 'create');

// Gracze – ranking globalny, profil z parametrem {id}
$routing->get('/leaderboard', PlayerController::class, 'leaderboard');
$routing->get('/players/{id}', PlayerController::class, 'profile');

// Zmiana języka interfejsu (zapis w sesji)
$routing->post('/language', LanguageController::class, 'change');

$routeFound = $routing->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);

if (!$routeFound) {
    $container->controller(DefaultController::class)->notFound();
}
