<h1><?= $t('leaderboard.title') ?></h1>
<p><?= $t('leaderboard.description') ?></p>

<?php if (!empty($players)): ?>
    <table>
        <thead>
            <tr>
                <th scope="col"><?= $t('stats.rank') ?></th>
                <th scope="col"><?= $t('stats.player') ?></th>
                <th scope="col"><?= $t('stats.matches') ?></th>
                <th scope="col"><?= $t('stats.wins') ?></th>
                <th scope="col"><?= $t('stats.win_rate') ?></th>
                <th scope="col"><?= $t('stats.avg3') ?></th>
                <th scope="col"><?= $t('stats.180s') ?></th>
                <th scope="col"><?= $t('stats.best_checkout') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $p): ?>
                <tr>
                    <td class="rank-cell"><?= (int) $p['rank'] ?></td>
                    <td>
                        <a href="/players/<?= (int) $p['id'] ?>">
                            <?= htmlspecialchars($p['name']) ?>
                        </a>
                    </td>
                    <td><?= (int) $p['matchesPlayed'] ?></td>
                    <td><?= (int) $p['wins'] ?></td>
                    <td><?= number_format($p['winRate'], 1) ?>%</td>
                    <td><?= number_format($p['avg3dart'], 1) ?></td>
                    <td><?= (int) $p['max180s'] ?></td>
                    <td><?= (int) $p['bestCheckout'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><?= $t('leaderboard.empty') ?></p>
<?php endif; ?>
