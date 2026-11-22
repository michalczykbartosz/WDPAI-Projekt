<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\FinishRule;
use App\Model\NewTournament;
use App\Repository\TournamentRepositoryInterface;
use DateTimeImmutable;

final class TournamentCreationService
{
    private const INVITE_CODE_ALPHABET = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
    private const INVITE_CODE_LENGTH = 6;
    private const INVITE_CODE_ATTEMPTS = 10;

    public function __construct(
        private readonly TournamentValidator $validator,
        private readonly TournamentRepositoryInterface $tournaments,
    ) {
    }

    /**
     * Tworzy turniej otwarty na zapisy. Organizatora bierzemy z sesji,
     * nigdy z formularza.
     *
     * @param array<string, string> $input pola z TournamentValidator::FIELDS
     * @return int identyfikator nowego turnieju
     * @throws ValidationException gdy dane formularza są niepoprawne
     */
    public function create(int $organizerId, array $input): int
    {
        $errors = $this->validator->validate($input, new DateTimeImmutable());

        if ($errors !== []) {
            throw new ValidationException($errors);
        }

        $tournament = new NewTournament(
            $organizerId,
            $input['name'],
            $input['description'] !== '' ? $input['description'] : null,
            $input['location'],
            TournamentValidator::parseDateTime($input['starts_at']),
            TournamentValidator::parseDateTime($input['registration_deadline']),
            TournamentValidator::parseInt($input['max_players']),
            TournamentValidator::parseInt($input['starting_score']),
            FinishRule::from($input['finish_rule']),
            TournamentValidator::parseInt($input['legs_to_win']),
        );

        return $this->tournaments->create($tournament, $this->generateInviteCode());
    }

    /**
     * Losuje kod z random_int (bezpieczny generator). Przy 36^6 kombinacji kolizja
     * jest bardzo mało prawdopodobna, ale i tak sprawdzamy, czy kod jest wolny.
     */
    private function generateInviteCode(): string
    {
        $lastIndex = strlen(self::INVITE_CODE_ALPHABET) - 1;

        for ($attempt = 0; $attempt < self::INVITE_CODE_ATTEMPTS; $attempt++) {
            $code = '';

            for ($i = 0; $i < self::INVITE_CODE_LENGTH; $i++) {
                $code .= self::INVITE_CODE_ALPHABET[random_int(0, $lastIndex)];
            }

            if (!$this->tournaments->inviteCodeExists($code)) {
                return $code;
            }
        }

        throw new \RuntimeException('Could not generate a unique invite code.');
    }
}
