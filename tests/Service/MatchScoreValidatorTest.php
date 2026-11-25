<?php

declare(strict_types=1);

namespace App\Tests\Service;

use App\Model\FinishRule;
use App\Service\MatchScoreValidator;
use PHPUnit\Framework\TestCase;

final class MatchScoreValidatorTest extends TestCase
{
    /** Poprawny wynik 3:1 – w każdym teście zmieniamy tylko badane pola. */
    private const VALID = [
        'score1' => '3',
        'score2' => '1',
        'avg1' => '85.50',
        'avg2' => '70.25',
        'max180_1' => '2',
        'max180_2' => '0',
        'checkout1' => '121',
        'checkout2' => '40',
    ];

    private const DOUBLE_OUT_IMPOSSIBLE = [169, 168, 166, 165, 163, 162, 159];

    private MatchScoreValidator $validator;

    protected function setUp(): void
    {
        $this->validator = new MatchScoreValidator();
    }

    // --- poprawne wyniki ---

    public function testAcceptsWinnerWithThreeLegs(): void
    {
        self::assertSame([], $this->validate([]));
    }

    public function testAcceptsSecondPlayerAsWinner(): void
    {
        self::assertSame([], $this->validate(['score1' => '2', 'score2' => '3']));
    }

    public function testAcceptsWhitewashWhenLoserHasNoCheckout(): void
    {
        self::assertSame([], $this->validate(['score2' => '0', 'checkout2' => '']));
    }

    public function testAcceptsBoundaryValues(): void
    {
        self::assertSame([], $this->validate(['avg1' => '180', 'avg2' => '0', 'checkout1' => '170', 'checkout2' => '2']));
        self::assertSame([], $this->validate(['max180_1' => '99']));
    }

    public function testAcceptsDecimalCommaAndEmpty180s(): void
    {
        self::assertSame([], $this->validate(['avg1' => '65,4', 'max180_2' => '']));
        self::assertSame(65.4, MatchScoreValidator::parseAverage('65,4'));
        self::assertSame(0, MatchScoreValidator::parse180s(''));
    }

    public function testUsesLegsToWinOfTheTournament(): void
    {
        $fiveLegs = ['score1' => '5', 'score2' => '4'];

        self::assertSame([], $this->validate($fiveLegs, 5));
        self::assertSame(
            ['score1' => 'validation.match.legs_invalid', 'score2' => 'validation.match.legs_invalid'],
            $this->validate($fiveLegs, 3),
        );
        self::assertSame(['score1' => 'validation.match.winner_legs'], $this->validate([], 5));
    }

    // --- legi ---

    public function testRejectsDraw(): void
    {
        self::assertSame(['score1' => 'validation.match.winner_legs'], $this->validate(['score2' => '3']));
    }

    public function testRejectsResultWithoutWinner(): void
    {
        self::assertSame(['score1' => 'validation.match.winner_legs'], $this->validate(['score1' => '2']));
    }

    public function testRejectsLegsOutsideRange(): void
    {
        foreach (['4', '-1', 'abc', '2.5', ' 3'] as $legs) {
            self::assertSame(
                ['score1' => 'validation.match.legs_invalid'],
                $this->validate(['score1' => $legs]),
                "legi: '$legs'",
            );
        }
    }

    public function testRequiresBothLegCounts(): void
    {
        $errors = $this->validate(['score1' => '', 'score2' => '']);

        self::assertSame('validation.required', $errors['score1']);
        self::assertSame('validation.required', $errors['score2']);
    }

    // --- checkout ---

    public function testRejectsEveryImpossibleDoubleOutCheckout(): void
    {
        foreach (self::DOUBLE_OUT_IMPOSSIBLE as $checkout) {
            self::assertSame(
                ['checkout1' => 'validation.match.checkout_impossible'],
                $this->validate(['checkout1' => (string) $checkout]),
                "checkout $checkout",
            );
        }
    }

    public function testRejectsDoubleOutCheckoutOutsideTwoToHundredSeventy(): void
    {
        foreach (['1', '171', '180'] as $checkout) {
            self::assertSame(
                ['checkout1' => 'validation.match.checkout_impossible'],
                $this->validate(['checkout1' => $checkout]),
                "checkout $checkout",
            );
        }
    }

    public function testAcceptsEveryOtherDoubleOutCheckoutFromTwoToHundredSeventy(): void
    {
        for ($checkout = 2; $checkout <= 170; $checkout++) {
            if (in_array($checkout, self::DOUBLE_OUT_IMPOSSIBLE, true)) {
                continue;
            }

            self::assertSame([], $this->validate(['checkout1' => (string) $checkout]), "checkout $checkout");
        }
    }

    public function testWinnerMustHaveCheckout(): void
    {
        self::assertSame(['checkout1' => 'validation.match.checkout_required'], $this->validate(['checkout1' => '']));
    }

    public function testPlayerWithoutLegCannotHaveCheckout(): void
    {
        self::assertSame(
            ['checkout2' => 'validation.match.checkout_without_leg'],
            $this->validate(['score2' => '0', 'checkout2' => '40']),
        );
    }

    public function testRejectsNonIntegerCheckout(): void
    {
        foreach (['40.5', 'abc', '-40'] as $checkout) {
            self::assertSame(
                ['checkout1' => 'validation.match.checkout_invalid'],
                $this->validate(['checkout1' => $checkout]),
                "checkout '$checkout'",
            );
        }
    }

    public function testSingleOutAllowsCheckoutsUpToHundredEighty(): void
    {
        self::assertSame([], $this->validate(['checkout1' => '180', 'checkout2' => '1'], 3, FinishRule::SingleOut));
        self::assertSame(
            ['checkout1' => 'validation.match.checkout_impossible'],
            $this->validate(['checkout1' => '179'], 3, FinishRule::SingleOut),
        );
    }

    /**
     * Niezależne sprawdzenie list niemożliwych checkoutów: liczymy wszystkie wyniki
     * możliwe do zamknięcia trzema lotkami i porównujemy z FinishRule.
     */
    public function testCheckoutRulesMatchEveryThreeDartCombination(): void
    {
        $singles = [...range(1, 20), 25];
        $doubles = [...array_map(static fn (int $x): int => 2 * $x, range(1, 20)), 50];
        $trebles = array_map(static fn (int $x): int => 3 * $x, range(1, 20));
        $anyDart = [0, ...$singles, ...$doubles, ...$trebles]; // 0 = lotka nierzucona

        $lastDart = [
            FinishRule::DoubleOut->value => $doubles,
            FinishRule::SingleOut->value => [...$singles, ...$doubles, ...$trebles],
        ];

        foreach (FinishRule::cases() as $rule) {
            $possible = [];

            foreach ($anyDart as $first) {
                foreach ($anyDart as $second) {
                    foreach ($lastDart[$rule->value] as $last) {
                        $possible[$first + $second + $last] = true;
                    }
                }
            }

            for ($points = 0; $points <= 200; $points++) {
                self::assertSame(
                    isset($possible[$points]),
                    $rule->isPossibleCheckout($points),
                    "{$rule->value}: $points",
                );
            }
        }
    }

    // --- średnia i 180-tki ---

    public function testRejectsInvalidAverages(): void
    {
        foreach (['180.01', '200', '85.555', '-5', 'abc', '1e2'] as $average) {
            self::assertSame(
                ['avg1' => 'validation.match.average_invalid'],
                $this->validate(['avg1' => $average]),
                "średnia '$average'",
            );
        }
    }

    public function testRequiresAverages(): void
    {
        self::assertSame(['avg2' => 'validation.required'], $this->validate(['avg2' => '']));
    }

    public function testRejectsInvalid180Counts(): void
    {
        foreach (['100', '-1', '1.5', 'x'] as $count) {
            self::assertSame(
                ['max180_1' => 'validation.match.count_180s_invalid'],
                $this->validate(['max180_1' => $count]),
                "180-tki '$count'",
            );
        }
    }

    public function testReportsErrorsForSeveralFieldsAtOnce(): void
    {
        $errors = $this->validate(['score2' => '3', 'avg1' => '', 'checkout2' => '169']);

        self::assertSame(['score1', 'avg1', 'checkout2'], array_keys($errors));
    }

    /** @param array<string, string> $changes */
    private function validate(array $changes, int $legsToWin = 3, FinishRule $rule = FinishRule::DoubleOut): array
    {
        return $this->validator->validate(array_merge(self::VALID, $changes), $legsToWin, $rule);
    }
}
