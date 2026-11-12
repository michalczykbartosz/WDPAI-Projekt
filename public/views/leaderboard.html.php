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
                <th scope="col"><?= $t('stats.points') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $player): ?>
                <tr>
                    <td class="rank-cell"><?= $player->rank ?></td>
                    <td>
                        <a href="/players/<?= $player->userId ?>">
                            <?= htmlspecialchars($player->displayName) ?>
                        </a>
                    </td>
                    <td><?= $player->matchesPlayed ?></td>
                    <td><?= $player->wins ?></td>
                    <td><?= number_format($player->winRate, 1) ?>%</td>
                    <td><?= $player->threeDartAverage !== null ? number_format($player->threeDartAverage, 1) : '–' ?></td>
                    <td><?= $player->total180s ?></td>
                    <td><?= $player->bestCheckout ?? '–' ?></td>
                    <td><strong><?= $player->points ?></strong></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
<?php else: ?>
    <p><?= $t('leaderboard.empty') ?></p>
<?php endif; ?>
