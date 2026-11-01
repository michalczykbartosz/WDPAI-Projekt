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
            </div>
        </div>
    </header>

    <main>
        <div class="container">
            <div class="error-page">
                <h1>404</h1>
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
            </div>
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
