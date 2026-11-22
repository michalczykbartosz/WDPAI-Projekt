<?php

declare(strict_types=1);

namespace App;

use App\Controller\AppController;
use App\Controller\AuthController;
use App\Controller\DefaultController;
use App\Controller\PlayerController;
use App\Controller\TournamentController;
use App\Database\Database;
use App\Database\DatabaseConfig;
use App\Repository\PdoPlayerRepository;
use App\Repository\PdoTournamentRepository;
use App\Repository\PdoUserRepository;
use App\Repository\PlayerRepositoryInterface;
use App\Repository\TournamentRepositoryInterface;
use App\Repository\UserRepositoryInterface;
use App\Security\CsrfTokenManager;
use App\Security\SessionManager;
use App\Service\LoginService;
use App\Service\RegistrationService;
use App\Service\RegistrationValidator;
use App\Service\TournamentCreationService;
use App\Service\TournamentValidator;

final class Container
{
    private ?Database $database = null;
    private ?SessionManager $session = null;
    private ?TournamentRepositoryInterface $tournamentRepository = null;
    private ?PlayerRepositoryInterface $playerRepository = null;
    private ?UserRepositoryInterface $userRepository = null;

    /** @param class-string<AppController> $controllerClass */
    public function controller(string $controllerClass): AppController
    {
        return match ($controllerClass) {
            DefaultController::class => new DefaultController($this->tournamentRepository(), $this->playerRepository()),
            TournamentController::class => new TournamentController(
                $this->tournamentRepository(),
                new TournamentCreationService(new TournamentValidator(), $this->tournamentRepository()),
            ),
            PlayerController::class => new PlayerController($this->playerRepository()),
            AuthController::class => new AuthController(
                new RegistrationService(new RegistrationValidator(), $this->userRepository()),
                new LoginService($this->userRepository()),
            ),
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

    private function userRepository(): UserRepositoryInterface
    {
        return $this->userRepository ??= new PdoUserRepository($this->database());
    }

    private function database(): Database
    {
        return $this->database ??= new Database(DatabaseConfig::fromEnvironment());
    }
}
