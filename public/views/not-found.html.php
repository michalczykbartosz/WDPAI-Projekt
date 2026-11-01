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
        <h1>404 – Strona nie znaleziona</h1>
        <p>Przepraszamy, szukana strona nie istnieje lub została usunięta.</p>

        <?php if (!empty($suggestedLinks)): ?>
            <nav>
                <h2>Może szukasz:</h2>
                <ul>
                    <?php foreach ($suggestedLinks as $link): ?>
                        <li>
                            <a href="<?= htmlspecialchars($link['url']) ?>">
                                <?= htmlspecialchars($link['label']) ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </main>

    <footer>
        <p>&copy; 2026 DartTournament. Projekt WDPAI.</p>
    </footer>
</body>
</html>
