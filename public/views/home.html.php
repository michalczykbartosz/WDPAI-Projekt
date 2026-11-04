<section class="hero">
    <h1>Witaj w <span class="accent">DartArena</span></h1>
    <p>Platforma do organizacji turniejów darta. Zapisz się na turniej, śledź drabinkę i sprawdzaj statystyki.</p>
</section>

<section>
    <div class="section-header">
        <h2>Nadchodzące turnieje</h2>
        <a href="/tournaments">Wszystkie turnieje →</a>
    </div>
    <?php if (!empty($upcomingTournaments)): ?>
        <div class="cards-grid">
            <?php foreach ($upcomingTournaments as $t): ?>
                <a href="/tournaments/<?= $t['id'] ?>" class="card card--link">
                    <h3 class="card__title"><?= htmlspecialchars($t['name']) ?></h3>
                    <p class="card__meta">📅 <?= htmlspecialchars($t['date']) ?></p>
                    <span class="badge badge--status-open"><?= htmlspecialchars($t['status']) ?></span>
                </a>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p>Brak nadchodzących turniejów.</p>
    <?php endif; ?>
</section>

<section>
    <div class="section-header">
        <h2>Top 3 graczy</h2>
        <a href="/leaderboard">Pełny ranking →</a>
    </div>
    <?php if (!empty($topPlayers)): ?>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Zawodnik</th>
                    <th>Średnia (3 lotki)</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($topPlayers as $p): ?>
                    <tr>
                        <td class="rank-cell"><?= $p['rank'] ?></td>
                        <td><?= htmlspecialchars($p['name']) ?></td>
                        <td><?= number_format($p['avg3dart'], 1) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>
