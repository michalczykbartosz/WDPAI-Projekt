<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Participant;
use App\Model\Tournament;
use App\Model\TournamentStatus;
use App\Repository\TournamentRepositoryInterface;

final class BracketService
{
    public function __construct(
        private readonly BracketGenerator $generator,
        private readonly TournamentRepositoryInterface $tournaments,
    ) {
    }

    public function canGenerate(Tournament $tournament, int $organizerId): bool
    {
        return $tournament->isOwnedBy($organizerId) && $tournament->status === TournamentStatus::Open;
    }

    /**
     * Losuje drabinkę z zapisanych graczy, zapisuje ją i zamyka zapisy (status ongoing).
     *
     * @throws BracketGenerationException
     */
    public function generate(int $tournamentId, int $organizerId): void
    {
        $tournament = $this->tournaments->findById($tournamentId);

        if ($tournament === null || $tournament->status === TournamentStatus::Draft) {
            throw new BracketGenerationException(BracketGenerationError::NotFound);
        }

        if (!$tournament->isOwnedBy($organizerId)) {
            throw new BracketGenerationException(BracketGenerationError::NotOwner);
        }

        if ($tournament->status !== TournamentStatus::Open) {
            throw new BracketGenerationException(BracketGenerationError::AlreadyGenerated);
        }

        $playerIds = array_map(
            static fn (Participant $participant): int => $participant->userId,
            $this->tournaments->findParticipants($tournamentId),
        );

        if (count($playerIds) < BracketGenerator::MIN_PLAYERS) {
            throw new BracketGenerationException(BracketGenerationError::NotEnoughPlayers);
        }

        // Repozytorium jeszcze raz sprawdza status pod blokadą – podwójne kliknięcie
        // nie utworzy dwóch drabinek.
        if (!$this->tournaments->saveBracket($tournamentId, $this->generator->generate($playerIds))) {
            throw new BracketGenerationException(BracketGenerationError::AlreadyGenerated);
        }
    }
}
