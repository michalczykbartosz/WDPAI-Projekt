<section class="hero">
    <h1><?= $t('home.hero.title_prefix') ?> <span class="accent">DartArena</span></h1>
    <p><?= $t('home.hero.description') ?></p>
</section>

<section>
    <div class="section-header">
        <h2><?= $t('home.upcoming.title') ?></h2>
        <a href="/tournaments"><?= $t('home.upcoming.all') ?></a>
    </div>
    <?php if (!empty($upcomingTournaments)): ?>
        <div class="cards-grid">
            <?php foreach ($upcomingTournaments as $tournament): ?>
                <a href="/tournaments/<?= $tournament->id ?>" class="card card--link">
                    <h3 class="card__title"><?= htmlspecialchars($tournament->name) ?></h3>
                    <p class="card__meta">📅 <?= $tournament->startsAt->format('Y-m-d') ?></p>
                    <span class="badge badge--status-<?= $tournament->status->value ?>"><?= $t('status.' . $tournament->status->value) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p><?= $t('home.upcoming.empty') ?></p>
    <?php endif; ?>
</section>

<section>
    <div class="section-header">
        <h2><?= $t('home.top.title') ?></h2>
        <a href="/leaderboard"><?= $t('home.top.all') ?></a>
    </div>
    <?php if (!empty($topPlayers)): ?>
        <table>
            <thead>
                <tr>
                    <th scope="col"><?= $t('stats.rank') ?></th>
                    <th scope="col"><?= $t('stats.player') ?></th>
                    <th scope="col"><?= $t('stats.avg3') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topPlayers as $player): ?>
                    <tr>
                        <td class="rank-cell"><?= $player->rank ?></td>
                        <td><a href="/players/<?= $player->userId ?>"><?= htmlspecialchars($player->displayName) ?></a></td>
                        <td><?= $player->threeDartAverage !== null ? number_format($player->threeDartAverage, 1) : '–' ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
