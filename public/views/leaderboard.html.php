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
                <li><a href="/leaderboard" class="active">Ranking</a></li>
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
            <h1>Globalny ranking graczy</h1>
            <p>Ranking wyliczany na podstawie zagregowanych statystyk ze wszystkich turniejów.</p>

            <?php if (!empty($players)): ?>
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Zawodnik</th>
                            <th>Mecze</th>
                            <th>Wygrane</th>
                            <th>% wygranych</th>
                            <th>Średnia (3 lotki)</th>
                            <th>180-ki</th>
                            <th>Najlepszy checkout</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($players as $p): ?>
                            <tr>
                                <td class="rank-cell"><?= $p['rank'] ?></td>
                                <td>
                                    <a href="/players/<?= $p['rank'] ?>">
                                        <?= htmlspecialchars($p['name']) ?>
                                    </a>
                                </td>
                                <td><?= $p['matchesPlayed'] ?></td>
                                <td><?= $p['wins'] ?></td>
                                <td><?= number_format($p['winRate'], 1) ?>%</td>
                                <td><?= number_format($p['avg3dart'], 1) ?></td>
                                <td><?= $p['max180s'] ?></td>
                                <td><?= $p['bestCheckout'] ?></td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Brak danych do wyświetlenia rankingu.</p>
            <?php endif; ?>
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
