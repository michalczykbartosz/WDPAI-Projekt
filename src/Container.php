<?php

declare(strict_types=1);

namespace App;

use App\Controller\AppController;
use App\Controller\DefaultController;
use App\Controller\TournamentController;
use App\Database\Database;
use App\Database\DatabaseConfig;
use App\Repository\PdoTournamentRepository;
use App\Repository\TournamentRepositoryInterface;

final class Container
{
    private ?Database $database = null;
    private ?TournamentRepositoryInterface $tournamentRepository = null;

    /** @param class-string<AppController> $controllerClass */
    public function controller(string $controllerClass): AppController
    {
        return match ($controllerClass) {
            DefaultController::class => new DefaultController($this->tournamentRepository()),
            TournamentController::class => new TournamentController($this->tournamentRepository()),
            default => new $controllerClass(),
        };
    }

    private function tournamentRepository(): TournamentRepositoryInterface
    {
        return $this->tournamentRepository ??= new PdoTournamentRepository($this->database());
    }

    private function database(): Database
    {
        return $this->database ??= new Database(DatabaseConfig::fromEnvironment());
    }
}
