<?php
$hasScore = $match['score1'] !== null && $match['score2'] !== null;
?>
<article class="bracket__match bracket__match--<?= $hasScore ? 'finished' : 'pending' ?>" aria-label="Mecz #<?= (int) $match['id'] ?>">
    <p>
        <strong><?= htmlspecialchars($match['player1']) ?></strong>
        <?php if ($hasScore): ?>
            <span class="text-primary"><?= (int) $match['score1'] ?> : <?= (int) $match['score2'] ?></span>
        <?php else: ?>
            <span class="text-muted">vs</span>
        <?php endif; ?>
        <strong><?= htmlspecialchars($match['player2']) ?></strong>
    </p>
    <p><em><?= htmlspecialchars($match['status']) ?></em></p>
</article>
