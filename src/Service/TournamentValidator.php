<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\FinishRule;
use App\Model\Tournament;
use DateTimeImmutable;

/**
 * Sprawdza dane formularza tworzenia turnieju. Limity odpowiadają ograniczeniom
 * CHECK w tabeli tournaments, więc błędne dane zatrzymujemy przed zapytaniem do bazy.
 */
final class TournamentValidator
{
    public const FIELDS = [
        'name',
        'description',
        'location',
        'max_players',
        'starting_score',
        'finish_rule',
        'legs_to_win',
        'starts_at',
        'registration_deadline',
    ];

    public const NAME_MIN_LENGTH = 3;
    public const NAME_MAX_LENGTH = 100;
    public const DESCRIPTION_MAX_LENGTH = 2000;
    public const LOCATION_MAX_LENGTH = 150;

    /** Format pola <input type="datetime-local">, np. 2026-12-05T18:30 */
    private const DATETIME_FORMAT = 'Y-m-d\TH:i';

    /**
     * @param array<string, string> $input pola z FIELDS
     * @return array<string, string> pole formularza => klucz komunikatu
     */
    public function validate(array $input, DateTimeImmutable $now): array
    {
        $startsAt = self::parseDateTime($input['starts_at']);

        return array_filter([
            'name' => $this->validateName($input['name']),
            'description' => mb_strlen($input['description']) > self::DESCRIPTION_MAX_LENGTH
                ? 'validation.tournament.description_too_long'
                : null,
            'location' => $this->validateLocation($input['location']),
            'max_players' => $this->validateOption($input['max_players'], Tournament::MAX_PLAYERS_OPTIONS),
            'starting_score' => $this->validateOption($input['starting_score'], Tournament::STARTING_SCORES),
            'finish_rule' => $this->validateFinishRule($input['finish_rule']),
            'legs_to_win' => $this->validateOption($input['legs_to_win'], Tournament::LEGS_TO_WIN_OPTIONS),
            'starts_at' => $this->validateStartsAt($input['starts_at'], $startsAt, $now),
            'registration_deadline' => $this->validateDeadline($input['registration_deadline'], $startsAt, $now),
        ]);
    }

    /**
     * Zamienia tekst na dodatnią liczbę całkowitą; "abc", "0", "-1", "1.5" dają null.
     */
    public static function parseInt(string $value): ?int
    {
        if (!ctype_digit($value)) {
            return null;
        }

        $number = filter_var($value, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1]]);

        return $number === false ? null : $number;
    }

    /**
     * Odrzuca też daty, które PHP po cichu by przesunął, np. 2026-02-31 -> 2026-03-03.
     */
    public static function parseDateTime(string $value): ?DateTimeImmutable
    {
        $dateTime = DateTimeImmutable::createFromFormat('!' . self::DATETIME_FORMAT, $value);

        if ($dateTime === false || $dateTime->format(self::DATETIME_FORMAT) !== $value) {
            return null;
        }

        return $dateTime;
    }

    private function validateName(string $name): ?string
    {
        if ($name === '') {
            return 'validation.required';
        }

        $length = mb_strlen($name);

        if ($length < self::NAME_MIN_LENGTH || $length > self::NAME_MAX_LENGTH) {
            return 'validation.tournament.name_length';
        }

        return null;
    }

    private function validateLocation(string $location): ?string
    {
        if ($location === '') {
            return 'validation.required';
        }

        return mb_strlen($location) > self::LOCATION_MAX_LENGTH
            ? 'validation.tournament.location_too_long'
            : null;
    }

    /** @param list<int> $allowed */
    private function validateOption(string $value, array $allowed): ?string
    {
        if ($value === '') {
            return 'validation.required';
        }

        return in_array(self::parseInt($value), $allowed, true) ? null : 'validation.invalid_option';
    }

    private function validateFinishRule(string $value): ?string
    {
        if ($value === '') {
            return 'validation.required';
        }

        return FinishRule::tryFrom($value) === null ? 'validation.invalid_option' : null;
    }

    private function validateStartsAt(string $value, ?DateTimeImmutable $startsAt, DateTimeImmutable $now): ?string
    {
        if ($value === '') {
            return 'validation.required';
        }

        if ($startsAt === null) {
            return 'validation.datetime.invalid';
        }

        return $startsAt <= $now ? 'validation.tournament.starts_at_past' : null;
    }

    private function validateDeadline(string $value, ?DateTimeImmutable $startsAt, DateTimeImmutable $now): ?string
    {
        if ($value === '') {
            return 'validation.required';
        }

        $deadline = self::parseDateTime($value);

        if ($deadline === null) {
            return 'validation.datetime.invalid';
        }

        if ($deadline <= $now) {
            return 'validation.tournament.deadline_past';
        }

        // Tak samo jak CHECK tournaments_deadline_before_start w bazie
        if ($startsAt !== null && $deadline > $startsAt) {
            return 'validation.tournament.deadline_after_start';
        }

        return null;
    }
}
