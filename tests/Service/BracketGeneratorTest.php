<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Model\Bracket;
use App\Model\PlannedMatch;
use App\Service\BracketGenerator;
use PHPUnit\Framework\TestCase;
use Random\Engine\Mt19937;
use Random\Randomizer;

final class BracketGeneratorTest extends TestCase
{
    public function testFourPlayersFillTwoSemifinalsAndAnEmptyFinal(): void
    {
        $bracket = $this->generator()->generate([11, 12, 13, 14]);

        self::assertSame(4, $bracket->size);
        self::assertSame(2, $bracket->rounds());
        self::assertCount(3, $bracket->matches);
        self::assertCount(2, $bracket->matchesInRound(1));
        self::assertSame([], $this->byes($bracket));
        self::assertPlayersAppearOnceInFirstRound([11, 12, 13, 14], $bracket);

        [$final] = $bracket->matchesInRound(2);
        self::assertSame(1, $final->position);
        self::assertNull($final->player1Id);
        self::assertNull($final->player2Id);
    }

    public function testEightPlayersBuildQuarterfinalsSemifinalsAndFinal(): void
    {
        $players = range(1, 8);
        $bracket = $this->generator()->generate($players);

        self::assertSame(8, $bracket->size);
        self::assertSame(3, $bracket->rounds());
        self::assertCount(7, $bracket->matches);
        self::assertCount(4, $bracket->matchesInRound(1));
        self::assertCount(2, $bracket->matchesInRound(2));
        self::assertCount(1, $bracket->matchesInRound(3));
        self::assertSame([], $this->byes($bracket));
        self::assertPlayersAppearOnceInFirstRound($players, $bracket);

        foreach ($bracket->matchesInRound(1) as $index => $match) {
            self::assertSame($index + 1, $match->position);
            self::assertNotNull($match->player1Id);
            self::assertNotNull($match->player2Id);
        }
    }

    public function testEveryPlayerGetsAUniqueSeedInDrawOrder(): void
    {
        $bracket = $this->generator()->generate(range(1, 8));

        $seeds = $bracket->seeds;
        ksort($seeds);
        self::assertSame(range(1, 8), array_keys($seeds));

        $seedNumbers = array_values($bracket->seeds);
        sort($seedNumbers);
        self::assertSame(range(1, 8), $seedNumbers);
    }

    public function testFivePlayersGetThreeByesThatAdvanceToTheSecondRound(): void
    {
        $players = [21, 22, 23, 24, 25];
        $bracket = $this->generator()->generate($players);

        self::assertSame(8, $bracket->size);
        self::assertCount(7, $bracket->matches);
        self::assertPlayersAppearOnceInFirstRound($players, $bracket);

        $byes = $this->byes($bracket);
        self::assertCount(3, $byes);

        $secondRound = $bracket->matchesInRound(2);

        foreach ($byes as $bye) {
            // Wolny los: jeden gracz, od razu zwycięzca
            self::assertNotNull($bye->player1Id);
            self::assertNull($bye->player2Id);
            self::assertSame($bye->player1Id, $bye->winnerId);

            // ...i od razu czeka w odpowiednim miejscu drugiej rundy
            $next = $secondRound[intdiv($bye->position + 1, 2) - 1];
            $slot = $bye->position % 2 === 1 ? $next->player1Id : $next->player2Id;
            self::assertSame($bye->winnerId, $slot);
        }
    }

    public function testThreePlayersGetOneBye(): void
    {
        $bracket = $this->generator()->generate([1, 2, 3]);

        self::assertSame(4, $bracket->size);
        self::assertCount(1, $this->byes($bracket));
        self::assertPlayersAppearOnceInFirstRound([1, 2, 3], $bracket);
    }

    public function testByesAreSpreadAcrossBothHalvesOfTheBracket(): void
    {
        // 6 graczy w drabince na 8: dwa wolne losy, po jednym w każdej połówce
        $bracket = $this->generator()->generate(range(1, 6));
        $byePositions = array_map(static fn (PlannedMatch $match): int => $match->position, $this->byes($bracket));

        self::assertCount(2, $byePositions);
        self::assertCount(1, array_filter($byePositions, static fn (int $position): bool => $position <= 2));
        self::assertCount(1, array_filter($byePositions, static fn (int $position): bool => $position > 2));
    }

    public function testNoFirstRoundMatchIsEmptyForAnyPlayerCount(): void
    {
        for ($count = 2; $count <= 32; $count++) {
            $bracket = $this->generator()->generate(range(1, $count));

            self::assertCount($bracket->size - 1, $bracket->matches, "$count graczy");
            self::assertCount($bracket->size - $count, $this->byes($bracket), "$count graczy");

            foreach ($bracket->matchesInRound(1) as $match) {
                self::assertNotNull($match->player1Id, "$count graczy, mecz {$match->position}");
            }
        }
    }

    public function testBracketSizeIsTheSmallestPowerOfTwoThatFitsAllPlayers(): void
    {
        $expected = [2 => 2, 3 => 4, 4 => 4, 5 => 8, 8 => 8, 9 => 16, 16 => 16, 17 => 32];

        foreach ($expected as $players => $size) {
            self::assertSame($size, BracketGenerator::bracketSize($players), "$players graczy");
        }
    }

    public function testTheSameRandomSeedGivesTheSameDraw(): void
    {
        $first = $this->generator(42)->generate(range(1, 8));
        $second = $this->generator(42)->generate(range(1, 8));

        self::assertEquals($first, $second);
    }

    public function testDifferentRandomSeedsGiveDifferentDraws(): void
    {
        $draws = [];

        for ($seed = 1; $seed <= 5; $seed++) {
            $draws[] = $this->generator($seed)->generate(range(1, 8))->seeds;
        }

        self::assertGreaterThan(1, count(array_unique(array_map('serialize', $draws))));
    }

    public function testRejectsFewerThanTwoPlayers(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator()->generate([7]);
    }

    public function testRejectsDuplicatePlayers(): void
    {
        $this->expectException(\InvalidArgumentException::class);

        $this->generator()->generate([1, 2, 2, 3]);
    }

    /** Stały seed generatora liczb losowych – wyniki testów są powtarzalne. */
    private function generator(int $seed = 2026): BracketGenerator
    {
        return new BracketGenerator(new Randomizer(new Mt19937($seed)));
    }

    /** @return list<PlannedMatch> */
    private function byes(Bracket $bracket): array
    {
        return array_values(array_filter(
            $bracket->matchesInRound(1),
            static fn (PlannedMatch $match): bool => $match->isBye(),
        ));
    }

    /** @param list<int> $players */
    private static function assertPlayersAppearOnceInFirstRound(array $players, Bracket $bracket): void
    {
        $inFirstRound = [];

        foreach ($bracket->matchesInRound(1) as $match) {
            $inFirstRound[] = $match->player1Id;

            if ($match->player2Id !== null) {
                $inFirstRound[] = $match->player2Id;
            }
        }

        sort($inFirstRound);
        sort($players);
        self::assertSame($players, $inFirstRound);
    }
}
