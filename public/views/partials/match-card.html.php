<?php
$hasScore = $match['score1'] !== null && $match['score2'] !== null;
?>
<article class="bracket__match bracket__match--<?= $hasScore ? 'finished' : 'pending' ?>" aria-label="<?= $t('bracket.match', ['id' => (int) $match['id']]) ?>">
    <p>
        <strong><?= $match['player1'] !== null ? htmlspecialchars($match['player1']) : $t('bracket.tbd') ?></strong>
        <?php if ($hasScore): ?>
            <span class="text-primary"><?= (int) $match['score1'] ?> : <?= (int) $match['score2'] ?></span>
        <?php else: ?>
            <span class="text-muted"><?= $t('bracket.vs') ?></span>
        <?php endif; ?>
        <strong><?= $match['player2'] !== null ? htmlspecialchars($match['player2']) : $t('bracket.tbd') ?></strong>
    </p>
    <p><em><?= $t('match.status.' . $match['status']) ?></em></p>
</article>
