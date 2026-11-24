<?php

declare(strict_types=1);

namespace App\Service;

use App\Model\Bracket;
use App\Model\PlannedMatch;
use Random\Randomizer;

/**
 * Losuje drabinkę pucharową (single elimination). Rozmiar drabinki to najmniejsza
 * potęga dwójki mieszcząca wszystkich graczy, a brakujące miejsca to wolne losy:
 * gracz z wolnym losem przechodzi do drugiej rundy bez gry.
 */
final class BracketGenerator
{
    public const MIN_PLAYERS = 2;

    public function __construct(private readonly Randomizer $randomizer = new Randomizer())
    {
    }

    /** @param list<int> $playerIds */
    public function generate(array $playerIds): Bracket
    {
        $count = count($playerIds);

        if ($count < self::MIN_PLAYERS) {
            throw new \InvalidArgumentException('A bracket needs at least ' . self::MIN_PLAYERS . ' players.');
        }

        if (count(array_unique($playerIds)) !== $count) {
            throw new \InvalidArgumentException('Player ids must be unique.');
        }

        $size = self::bracketSize($count);
        $drawn = $this->randomizer->shuffleArray(array_values($playerIds));

        $firstRound = $this->firstRound($drawn, $size);
        $matches = [...$firstRound, ...$this->laterRounds($firstRound, $size)];

        $seeds = [];
        foreach ($drawn as $index => $playerId) {
            $seeds[$playerId] = $index + 1;
        }

        return new Bracket($size, $matches, $seeds);
    }

    public static function bracketSize(int $playerCount): int
    {
        $size = 2;

        while ($size < $playerCount) {
            $size *= 2;
        }

        return $size;
    }

    /**
     * Pierwsza runda. Wolnych losów jest zawsze mniej niż meczów, więc w każdym meczu
     * jest co najmniej jeden gracz.
     *
     * @param list<int> $drawn gracze w wylosowanej kolejności
     * @return list<PlannedMatch>
     */
    private function firstRound(array $drawn, int $size): array
    {
        $matchCount = intdiv($size, 2);
        $byePositions = self::byePositions($matchCount, $size - count($drawn));
        $matches = [];

        for ($position = 1; $position <= $matchCount; $position++) {
            $player1 = array_shift($drawn);

            if (isset($byePositions[$position])) {
                $matches[] = new PlannedMatch(1, $position, $player1, null, $player1);
                continue;
            }

            $matches[] = new PlannedMatch(1, $position, $player1, array_shift($drawn));
        }

        return $matches;
    }

    /**
     * Rozkłada wolne losy równomiernie po drabince, żeby nie trafiały wszystkie do jednej połówki.
     *
     * @return array<int, true> numery meczów pierwszej rundy z wolnym losem
     */
    private static function byePositions(int $matchCount, int $byes): array
    {
        $positions = [];

        for ($i = 0; $i < $byes; $i++) {
            $positions[intdiv($i * $matchCount, $byes) + 1] = true;
        }

        return $positions;
    }

    /**
     * Puste mecze kolejnych rund. Zwycięzcy wolnych losów od razu trafiają do drugiej rundy:
     * mecz z pozycji p przechodzi na pozycję ceil(p / 2), jako gracz 1 (p nieparzyste) albo 2.
     *
     * @param list<PlannedMatch> $firstRound
     * @return list<PlannedMatch>
     */
    private function laterRounds(array $firstRound, int $size): array
    {
        $advancing = [];
        foreach ($firstRound as $match) {
            if ($match->isBye()) {
                $advancing[intdiv($match->position + 1, 2)][$match->position % 2 === 1 ? 1 : 2] = $match->winnerId;
            }
        }

        $matches = [];
        $rounds = (int) log($size, 2);

        for ($round = 2; $round <= $rounds; $round++) {
            $matchCount = intdiv($size, 2 ** $round);

            for ($position = 1; $position <= $matchCount; $position++) {
                $players = $round === 2 ? ($advancing[$position] ?? []) : [];
                $matches[] = new PlannedMatch($round, $position, $players[1] ?? null, $players[2] ?? null);
            }
        }

        return $matches;
    }
}
