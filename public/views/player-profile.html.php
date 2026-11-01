<!doctype html>
<html lang="pl" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <header class="navbar">
        <div class="container">
            <a href="/" class="navbar__brand">🎯 DartArena</a>
            <button class="hamburger" aria-label="Menu" onclick="document.querySelector('.navbar__links').classList.toggle('open')">☰</button>
            <ul class="navbar__links">
                <li><a href="/tournaments">Turnieje</a></li>
                <li><a href="/leaderboard">Ranking</a></li>
                <li><a href="/organizer/create">Panel organizatora</a></li>
            </ul>
            <div class="navbar__actions">
                <button class="theme-toggle" onclick="toggleTheme()" aria-label="Przełącz motyw">🌙</button>
                <span class="badge badge--player">Gracz</span>
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <article>
                <h1>Profil zawodnika</h1>

                <section class="card mb-lg">
                    <div style="display:flex; gap:var(--space-xl); align-items:flex-start; flex-wrap:wrap;">
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
                                <th>Data</th>
                                <th>Turniej</th>
                                <th>Przeciwnik</th>
                                <th>Wynik</th>
                                <th>Średnia</th>
                                <th>180-ki</th>
                                <th>Checkout</th>
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
        </div>
    </main>

    <footer>
        <div class="container">
            <p>&copy; 2026 DartArena. Projekt WDPAI.</p>
        </div>
    </footer>

    <script src="/js/theme.js"></script>
</body>
</html>
