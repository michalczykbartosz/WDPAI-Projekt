<article>
    <h1>Profil zawodnika</h1>

    <section class="card mb-lg">
        <div class="profile-header">
            <figure>
                <?php if ($player['avatar']): ?>
                    <img src="<?= htmlspecialchars($player['avatar']) ?>"
                         alt="Awatar <?= htmlspecialchars($player['name']) ?>"
                         class="avatar">
                <?php else: ?>
                    <div class="avatar-placeholder">🎯</div>
                <?php endif; ?>
            </figure>

            <div>
                <h2><?= htmlspecialchars($player['name']) ?></h2>
                <p class="text-muted">Członek od: <?= htmlspecialchars($player['joinedDate']) ?></p>

                <form action="/players/<?= $player['id'] ?>/avatar" method="post" enctype="multipart/form-data" class="mt-md">
                    <label for="avatar">Zmień zdjęcie profilowe:</label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
                    <button type="submit" class="btn btn--secondary mt-sm">Prześlij</button>
                </form>
            </div>
        </div>
    </section>

    <section>
        <h2>Statystyki kariery</h2>
        <div class="stats-grid">
            <div class="stat-card">
                <span class="stat-card__value"><?= $player['matchesPlayed'] ?></span>
                <span class="stat-card__label">Mecze</span>
            </div>
            <div class="stat-card">
                <span class="stat-card__value"><?= $player['wins'] ?></span>
                <span class="stat-card__label">Wygrane</span>
            </div>
            <div class="stat-card">
                <span class="stat-card__value"><?= $player['losses'] ?></span>
                <span class="stat-card__label">Przegrane</span>
            </div>
            <div class="stat-card">
                <span class="stat-card__value"><?= number_format($player['winRate'], 1) ?>%</span>
                <span class="stat-card__label">% wygranych</span>
            </div>
            <div class="stat-card">
                <span class="stat-card__value"><?= number_format($player['avg3dart'], 1) ?></span>
                <span class="stat-card__label">Średnia 3 lotek</span>
            </div>
            <div class="stat-card">
                <span class="stat-card__value"><?= $player['total180s'] ?></span>
                <span class="stat-card__label">180-ki</span>
            </div>
            <div class="stat-card">
                <span class="stat-card__value"><?= $player['bestCheckout'] ?></span>
                <span class="stat-card__label">Rekordowy checkout</span>
            </div>
        </div>
    </section>
</article>

<section>
    <h2>Ostatnie mecze</h2>
    <?php if (!empty($recentMatches)): ?>
        <table>
            <thead>
                <tr>
                    <th scope="col">Data</th>
                    <th scope="col">Turniej</th>
                    <th scope="col">Przeciwnik</th>
                    <th scope="col">Wynik</th>
                    <th scope="col">Średnia</th>
                    <th scope="col">180-ki</th>
                    <th scope="col">Checkout</th>
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
                        <td><?= $m['max180s'] ?></td>
                        <td><?= $m['checkout'] ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>Brak historii meczów.</p>
    <?php endif; ?>
</section>
