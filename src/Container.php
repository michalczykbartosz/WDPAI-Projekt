<?php

declare(strict_types=1);

namespace App;

use App\Controller\AppController;
use App\Controller\DefaultController;
use App\Controller\PlayerController;
use App\Controller\TournamentController;
use App\Database\Database;
use App\Database\DatabaseConfig;
use App\Repository\PdoPlayerRepository;
use App\Repository\PdoTournamentRepository;
use App\Repository\PlayerRepositoryInterface;
use App\Repository\TournamentRepositoryInterface;
use App\Security\CsrfTokenManager;
use App\Security\SessionManager;

final class Container
{
    private ?Database $database = null;
    private ?SessionManager $session = null;
    private ?TournamentRepositoryInterface $tournamentRepository = null;
    private ?PlayerRepositoryInterface $playerRepository = null;

    /** @param class-string<AppController> $controllerClass */
    public function controller(string $controllerClass): AppController
    {
        return match ($controllerClass) {
            DefaultController::class => new DefaultController($this->tournamentRepository(), $this->playerRepository()),
            TournamentController::class => new TournamentController($this->tournamentRepository()),
            PlayerController::class => new PlayerController($this->playerRepository()),
            default => new $controllerClass(),
        };
    }

    public function session(): SessionManager
    {
        return $this->session ??= new SessionManager();
    }

    public function csrf(): CsrfTokenManager
    {
        return new CsrfTokenManager($this->session());
    }

    private function tournamentRepository(): TournamentRepositoryInterface
    {
        return $this->tournamentRepository ??= new PdoTournamentRepository($this->database());
    }

    private function playerRepository(): PlayerRepositoryInterface
    {
        return $this->playerRepository ??= new PdoPlayerRepository($this->database());
    }

    private function database(): Database
    {
        return $this->database ??= new Database(DatabaseConfig::fromEnvironment());
    }
}
