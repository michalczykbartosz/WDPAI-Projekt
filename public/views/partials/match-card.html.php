<?php
$hasScore = $match->player1Legs !== null && $match->player2Legs !== null;
?>
<article class="bracket__match bracket__match--<?= $hasScore ? 'finished' : 'pending' ?>" aria-label="<?= $t('bracket.match', ['id' => $match->id]) ?>">
    <p>
        <strong><?= $match->player1Name !== null ? htmlspecialchars($match->player1Name) : $t('bracket.tbd') ?></strong>
        <?php if ($hasScore): ?>
            <span class="text-primary"><?= $match->player1Legs ?> : <?= $match->player2Legs ?></span>
        <?php else: ?>
            <span class="text-muted"><?= $t('bracket.vs') ?></span>
        <?php endif; ?>
        <strong><?= $match->player2Name !== null ? htmlspecialchars($match->player2Name) : $t('bracket.tbd') ?></strong>
    </p>
    <p><em><?= $t('match.status.' . $match->status->value) ?></em></p>
</article>
