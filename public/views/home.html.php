<!doctype html>
<html lang="pl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
</head>
<body>
    <header>
        <nav>
            <a href="/"><strong>🎯 DartTournament</strong></a>
            <ul>
                <li><a href="/tournaments">Turnieje</a></li>
                <li><a href="/leaderboard">Ranking</a></li>
                <li><a href="/organizer/create">Panel organizatora</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <h1>Witaj w DartTournament</h1>
            <p>Platforma do organizacji turniejów darta. Zapisz się na turniej, śledź drabinkę i sprawdzaj statystyki.</p>
        </section>

        <section>
            <h2>Nadchodzące turnieje</h2>
            <?php if (!empty($upcomingTournaments)): ?>
                <ul>
                    <?php foreach ($upcomingTournaments as $t): ?>
                        <li>
                            <a href="/tournaments/<?= $t['id'] ?>">
                                <strong><?= htmlspecialchars($t['name']) ?></strong>
                            </a>
                            — <?= htmlspecialchars($t['date']) ?>
                            <span>(<?= htmlspecialchars($t['status']) ?>)</span>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <p><a href="/tournaments">Zobacz wszystkie turnieje →</a></p>
            <?php else: ?>
                <p>Brak nadchodzących turniejów.</p>
            <?php endif; ?>
        </section>

        <section>
            <h2>Top 3 graczy</h2>
            <?php if (!empty($topPlayers)): ?>
                <ol>
                    <?php foreach ($topPlayers as $p): ?>
                        <li>
                            <?= htmlspecialchars($p['name']) ?>
                            — średnia 3 lotek: <?= number_format($p['avg3dart'], 1) ?>
                        </li>
                    <?php endforeach; ?>
                </ol>
                <p><a href="/leaderboard">Pełny ranking →</a></p>
            <?php else: ?>
                <p>Ranking nie jest jeszcze dostępny.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 DartTournament. Projekt WDPAI.</p>
    </footer>
</body>
</html>
