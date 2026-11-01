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
                <li><a href="/tournaments" class="active">Turnieje</a></li>
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
            <h1>Eksplorator turniejów</h1>

            <section>
                <form method="get" action="/tournaments" class="filter-bar">
                    <div>
                        <label for="format">Format:</label>
                        <select name="format" id="format">
                            <option value="">Wszystkie</option>
                            <option value="501do">501 Double Out</option>
                            <option value="301do">301 Double Out</option>
                            <option value="501so">501 Single Out</option>
                        </select>
                    </div>

                    <div>
                        <label for="status">Status:</label>
                        <select name="status" id="status">
                            <option value="">Wszystkie</option>
                            <option value="open">Zapisy otwarte</option>
                            <option value="ongoing">W trakcie</option>
                            <option value="finished">Zakończony</option>
                        </select>
                    </div>

                    <button type="submit" class="btn btn--primary">Filtruj</button>
                </form>
            </section>

            <section>
                <?php if (!empty($tournaments)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>Nazwa</th>
                                <th>Format</th>
                                <th>Data</th>
                                <th>Lokalizacja</th>
                                <th>Zapisy</th>
                                <th>Status</th>
                                <th>Akcja</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($tournaments as $t): ?>
                                <tr>
                                    <td>
                                        <a href="/tournaments/<?= $t['id'] ?>">
                                            <?= htmlspecialchars($t['name']) ?>
                                        </a>
                                    </td>
                                    <td><?= htmlspecialchars($t['format']) ?></td>
                                    <td><?= htmlspecialchars($t['date']) ?></td>
                                    <td><?= htmlspecialchars($t['location']) ?></td>
                                    <td><?= $t['registeredPlayers'] ?> / <?= $t['maxPlayers'] ?></td>
                                    <td>
                                        <?php
                                            $statusClass = match($t['status']) {
                                                'Zapisy otwarte' => 'badge--status-open',
                                                'W trakcie' => 'badge--status-ongoing',
                                                'Zakończony' => 'badge--status-finished',
                                                default => 'badge--status-closed',
                                            };
                                        ?>
                                        <span class="badge <?= $statusClass ?>"><?= htmlspecialchars($t['status']) ?></span>
                                    </td>
                                    <td>
                                        <a href="/tournaments/<?= $t['id'] ?>" class="btn btn--secondary">Szczegóły</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Brak turniejów spełniających kryteria.</p>
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
