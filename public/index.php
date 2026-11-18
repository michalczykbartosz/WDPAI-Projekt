<?php

declare(strict_types=1);

use App\Controller\AuthController;
use App\Controller\DefaultController;
use App\Controller\LanguageController;
use App\Controller\PlayerController;
use App\Controller\TournamentController;
use App\Container;
use App\Routing;

require_once dirname(__DIR__) . '/vendor/autoload.php';

$container = new Container();
$container->session()->start();

$routing = new Routing($container);

// Strona główna
$routing->get('/', DefaultController::class, 'index');

// Turnieje – lista, szczegóły z parametrem {id}, tworzenie
$routing->get('/tournaments', TournamentController::class, 'index');
$routing->get('/tournaments/{id}', TournamentController::class, 'show');
$routing->get('/tournaments/code/{code:[A-Z0-9]{6}}', TournamentController::class, 'showByCode');
$routing->get('/organizer/create', TournamentController::class, 'create');

// Gracze – ranking globalny, profil z parametrem {id}
$routing->get('/leaderboard', PlayerController::class, 'leaderboard');
$routing->get('/players/{id}', PlayerController::class, 'profile');

// Konta użytkowników
$routing->get('/register', AuthController::class, 'showRegistrationForm');
$routing->post('/register', AuthController::class, 'register');
$routing->get('/login', AuthController::class, 'showLoginForm');
$routing->post('/login', AuthController::class, 'login');

// Zmiana języka interfejsu (zapis w sesji)
$routing->post('/language', LanguageController::class, 'change');

$routeFound = $routing->dispatch(
    $_SERVER['REQUEST_METHOD'],
    $_SERVER['REQUEST_URI'],
);

if (!$routeFound) {
    $container->controller(DefaultController::class)->notFound();
}
