<?php
$hasScore = $match->player1Legs !== null && $match->player2Legs !== null;
$emptySlot = $match->isBye() ? $t('bracket.bye') : $t('bracket.tbd');
?>
<article class="bracket__match bracket__match--<?= $hasScore ? 'finished' : 'pending' ?>" aria-label="<?= $t('bracket.match', ['id' => $match->id]) ?>">
    <p>
        <strong><?= $match->player1Name !== null ? htmlspecialchars($match->player1Name) : $emptySlot ?></strong>
        <?php if ($hasScore): ?>
            <span class="text-primary"><?= $match->player1Legs ?> : <?= $match->player2Legs ?></span>
        <?php else: ?>
            <span class="text-muted"><?= $t('bracket.vs') ?></span>
        <?php endif; ?>
        <strong><?= $match->player2Name !== null ? htmlspecialchars($match->player2Name) : $emptySlot ?></strong>
    </p>
    <p><em><?= $match->isBye() ? $t('bracket.bye_advance') : $t('match.status.' . $match->status->value) ?></em></p>
    <?php if ($canEnterResult($match)): ?>
        <button type="button" class="btn btn--secondary bracket__result-button"
                data-result-match="<?= $match->id ?>"
                data-player1="<?= htmlspecialchars((string) $match->player1Name) ?>"
                data-player2="<?= htmlspecialchars((string) $match->player2Name) ?>"><?= $t('result.enter') ?></button>
    <?php endif; ?>
</article>
