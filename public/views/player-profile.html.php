<?php
$careerStats = [
    'stats.matches' => (string) (int) $player['matchesPlayed'],
    'stats.wins' => (string) (int) $player['wins'],
    'stats.losses' => (string) (int) $player['losses'],
    'stats.win_rate' => number_format($player['winRate'], 1) . '%',
    'stats.avg3' => number_format($player['avg3dart'], 1),
    'stats.180s' => (string) (int) $player['total180s'],
    'stats.best_checkout' => (string) (int) $player['bestCheckout'],
];
?>
<article>
    <h1><?= $t('profile.title') ?></h1>

    <section class="card mb-lg">
        <div class="profile-header">
            <figure>
                <?php if ($player['avatar']): ?>
                    <img src="<?= htmlspecialchars($player['avatar']) ?>"
                         alt="<?= $t('profile.avatar_alt', ['name' => $player['name']]) ?>"
                         class="avatar">
                <?php else: ?>
                    <div class="avatar-placeholder" aria-hidden="true">🎯</div>
                <?php endif; ?>
            </figure>

            <div>
                <h2><?= htmlspecialchars($player['name']) ?></h2>
                <p class="text-muted"><?= $t('profile.member_since', ['date' => $player['joinedDate']]) ?></p>

                <form action="/players/<?= (int) $player['id'] ?>/avatar" method="post" enctype="multipart/form-data" class="mt-md">
                    <label for="avatar"><?= $t('profile.change_avatar') ?></label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
                    <button type="submit" class="btn btn--secondary mt-sm"><?= $t('profile.upload') ?></button>
                </form>
            </div>
        </div>
    </section>

    <section>
        <h2><?= $t('profile.career') ?></h2>
        <div class="stats-grid">
            <?php foreach ($careerStats as $labelKey => $value): ?>
                <div class="stat-card">
                    <span class="stat-card__value"><?= htmlspecialchars($value) ?></span>
                    <span class="stat-card__label"><?= $t($labelKey) ?></span>
                </div>
            <?php endforeach; ?>
        </div>
    </section>
</article>

<section>
    <h2><?= $t('profile.recent') ?></h2>
    <?php if (!empty($recentMatches)): ?>
        <table>
            <thead>
                <tr>
                    <th scope="col"><?= $t('stats.date') ?></th>
                    <th scope="col"><?= $t('stats.tournament') ?></th>
                    <th scope="col"><?= $t('stats.opponent') ?></th>
                    <th scope="col"><?= $t('stats.result') ?></th>
                    <th scope="col"><?= $t('stats.average') ?></th>
                    <th scope="col"><?= $t('stats.180s') ?></th>
                    <th scope="col"><?= $t('stats.checkout') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($recentMatches as $m): ?>
                    <tr>
                        <td><?= htmlspecialchars($m['date']) ?></td>
                        <td><?= htmlspecialchars($m['tournament']) ?></td>
                        <td><?= htmlspecialchars($m['opponent']) ?></td>
                        <td><strong><?= htmlspecialchars($m['result']) ?></strong></td>
                        <td><?= number_format($m['avg'], 1) ?></td>
                        <td><?= (int) $m['max180s'] ?></td>
                        <td><?= (int) $m['checkout'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?= $t('profile.no_matches') ?></p>
    <?php endif; ?>
</section>
