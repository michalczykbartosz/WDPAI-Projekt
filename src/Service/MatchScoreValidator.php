<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\FinishRule;

/**
 * Sprawdza wynik meczu wpisany przez gracza: legi, średnią, liczbę 180-tek
 * i najwyższy checkout obu graczy. Zwraca błędy jako pole => klucz tłumaczenia;
 * komunikaty o legach używają parametru {legs} (liczba legów do wygrania meczu).
 */
final class MatchScoreValidator
{
    public const FIELDS = [
        'score1', 'score2',
        'avg1', 'avg2',
        'max180_1', 'max180_2',
        'checkout1', 'checkout2',
    ];

    public const MAX_AVERAGE = 180.0;
    public const MAX_180S = 99;

    /** Średnia jak kolumna NUMERIC(5,2): do trzech cyfr i dwóch miejsc po przecinku */
    private const AVERAGE_PATTERN = '/^\d{1,3}(\.\d{1,2})?$/';

    /**
     * @param array<string, string> $input pola z FIELDS
     * @return array<string, string> pole formularza => klucz komunikatu
     */
    public function validate(array $input, int $legsToWin, FinishRule $finishRule): array
    {
        $legs = [
            1 => self::parseLegs($input['score1'], $legsToWin),
            2 => self::parseLegs($input['score2'], $legsToWin),
        ];

        $errors = [
            'score1' => $legs[1] === null ? $this->legsError($input['score1']) : null,
            'score2' => $legs[2] === null ? $this->legsError($input['score2']) : null,
        ];

        if ($legs[1] !== null && $legs[2] !== null && !self::hasExactlyOneWinner($legs[1], $legs[2], $legsToWin)) {
            $errors['score1'] = 'validation.match.winner_legs';
        }

        foreach ([1, 2] as $player) {
            $errors['avg' . $player] = $this->validateAverage($input['avg' . $player]);
            $errors['max180_' . $player] = $this->validate180s($input['max180_' . $player]);
            $errors['checkout' . $player] = $this->validateCheckout($input['checkout' . $player], $legs[$player], $finishRule);
        }

        return array_filter($errors);
    }

    /** Liczba legów od 0 do $legsToWin; inaczej null. */
    public static function parseLegs(string $value, int $legsToWin): ?int
    {
        if (!ctype_digit($value)) {
            return null;
        }

        $legs = (int) $value;

        return $legs <= $legsToWin ? $legs : null;
    }

    /** Akceptuje też przecinek dziesiętny, np. "65,5". */
    public static function parseAverage(string $value): ?float
    {
        $value = str_replace(',', '.', $value);

        if (preg_match(self::AVERAGE_PATTERN, $value) !== 1) {
            return null;
        }

        $average = (float) $value;

        return $average <= self::MAX_AVERAGE ? $average : null;
    }

    /** Pusta wartość oznacza zero 180-tek. */
    public static function parse180s(string $value): ?int
    {
        if ($value === '') {
            return 0;
        }

        if (!ctype_digit($value) || strlen($value) > 2) {
            return null;
        }

        return (int) $value;
    }

    public static function hasExactlyOneWinner(int $player1Legs, int $player2Legs, int $legsToWin): bool
    {
        return ($player1Legs === $legsToWin) !== ($player2Legs === $legsToWin);
    }

    private function legsError(string $value): string
    {
        return $value === '' ? 'validation.required' : 'validation.match.legs_invalid';
    }

    private function validateAverage(string $value): ?string
    {
        if ($value === '') {
            return 'validation.required';
        }

        return self::parseAverage($value) === null ? 'validation.match.average_invalid' : null;
    }

    private function validate180s(string $value): ?string
    {
        return self::parse180s($value) === null ? 'validation.match.count_180s_invalid' : null;
    }

    /**
     * Checkout ma sens tylko u gracza, który wygrał co najmniej jednego lega
     * – wtedy jest wymagany i musi być możliwy do zamknięcia trzema lotkami.
     */
    private function validateCheckout(string $value, ?int $legsWon, FinishRule $finishRule): ?string
    {
        if ($legsWon === null) {
            return null;
        }

        if ($legsWon === 0) {
            return $value === '' ? null : 'validation.match.checkout_without_leg';
        }

        if ($value === '') {
            return 'validation.match.checkout_required';
        }

        if (!ctype_digit($value)) {
            return 'validation.match.checkout_invalid';
        }

        return $finishRule->isPossibleCheckout((int) $value) ? null : 'validation.match.checkout_impossible';
    }
}
