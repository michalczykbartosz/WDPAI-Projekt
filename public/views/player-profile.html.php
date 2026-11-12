<?php
$careerStats = [
    'stats.matches' => (string) $stats->matchesPlayed,
    'stats.wins' => (string) $stats->wins,
    'stats.losses' => (string) $stats->losses,
    'stats.win_rate' => number_format($stats->winRate, 1) . '%',
    'stats.avg3' => $stats->threeDartAverage !== null ? number_format($stats->threeDartAverage, 1) : '–',
    'stats.180s' => (string) $stats->total180s,
    'stats.best_checkout' => $stats->bestCheckout !== null ? (string) $stats->bestCheckout : '–',
    'stats.points' => (string) $stats->points,
];
?>
<article>
    <h1><?= $t('profile.title') ?></h1>

    <section class="card mb-lg">
        <div class="profile-header">
            <figure>
                <?php if ($profile->avatarPath !== null): ?>
                    <img src="<?= htmlspecialchars($profile->avatarPath) ?>"
                         alt="<?= $t('profile.avatar_alt', ['name' => $profile->displayName]) ?>"
                         class="avatar">
                <?php else: ?>
                    <div class="avatar-placeholder" aria-hidden="true">🎯</div>
                <?php endif; ?>
            </figure>

            <div>
                <h2><?= htmlspecialchars($profile->displayName) ?></h2>
                <?php if ($profile->club !== null): ?>
                    <p><?= htmlspecialchars($profile->club) ?></p>
                <?php endif; ?>
                <p class="text-muted"><?= $t('profile.member_since', ['date' => $profile->memberSince->format('Y-m-d')]) ?></p>

                <form action="/players/<?= $profile->userId ?>/avatar" method="post" enctype="multipart/form-data" class="mt-md">
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
                <?php foreach ($recentMatches as $match): ?>
                    <tr>
                        <td><?= $match->playedAt->format('Y-m-d') ?></td>
                        <td>
                            <a href="/tournaments/<?= $match->tournamentId ?>"><?= htmlspecialchars($match->tournamentName) ?></a>
                        </td>
                        <td>
                            <?php if ($match->opponentId !== null): ?>
                                <a href="/players/<?= $match->opponentId ?>"><?= htmlspecialchars($match->opponentName ?? '') ?></a>
                            <?php else: ?>
                                –
                            <?php endif; ?>
                        </td>
                        <td>
                            <strong><?= $match->score() ?></strong>
                            <span class="badge badge--result-<?= $match->won ? 'win' : 'loss' ?>"><?= $t($match->won ? 'profile.win' : 'profile.loss') ?></span>
                        </td>
                        <td><?= $match->threeDartAverage !== null ? number_format($match->threeDartAverage, 1) : '–' ?></td>
                        <td><?= $match->count180s ?? '–' ?></td>
                        <td><?= $match->highestCheckout ?? '–' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?= $t('profile.no_matches') ?></p>
    <?php endif; ?>
</section>
