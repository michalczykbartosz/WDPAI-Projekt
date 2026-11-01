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
                            <a href="/tournaments/<?= $t['id'] ?>" class="card" style="text-decoration:none;">
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
