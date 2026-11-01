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
            <article>
                <h1><?= htmlspecialchars($tournament['name']) ?></h1>

                <section class="card mb-lg">
                    <h2>Informacje o turnieju</h2>
                    <dl>
                        <dt>Format</dt>
                        <dd><?= htmlspecialchars($tournament['format']) ?></dd>

                        <dt>Data</dt>
                        <dd><?= htmlspecialchars($tournament['date']) ?></dd>

                        <dt>Lokalizacja</dt>
                        <dd><?= htmlspecialchars($tournament['location']) ?></dd>

                        <dt>Status</dt>
                        <dd><span class="badge badge--status-ongoing"><?= htmlspecialchars($tournament['status']) ?></span></dd>

                        <dt>Maks. graczy</dt>
                        <dd><?= $tournament['maxPlayers'] ?></dd>
                    </dl>

                    <button type="button" id="btn-register" class="btn btn--primary">Zapisz się na turniej</button>
                    <p id="register-feedback" role="status"></p>
                </section>
            </article>

            <section>
                <h2>Zapisani zawodnicy</h2>
                <?php if (!empty($players)): ?>
                    <table>
                        <thead>
                            <tr>
                                <th>#</th>
                                <th>Zawodnik</th>
                                <th>Średnia (3 lotki)</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($players as $p): ?>
                                <tr>
                                    <td class="rank-cell"><?= $p['seed'] ?></td>
                                    <td><?= htmlspecialchars($p['name']) ?></td>
                                    <td><?= number_format($p['avg'], 1) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                <?php else: ?>
                    <p>Brak zapisanych zawodników.</p>
                <?php endif; ?>
            </section>

            <section>
                <h2>Drabinka turniejowa (Single Elimination)</h2>
                <div class="bracket">
                    <div class="bracket__round">
                        <h3>Ćwierćfinały</h3>
                        <?php foreach ($bracket['quarterfinals'] as $match): ?>
                            <div class="bracket__match <?= $match['status'] === 'Zakończony' ? 'bracket__match--finished' : 'bracket__match--pending' ?>">
                                <p>
                                    <strong><?= htmlspecialchars($match['player1']) ?></strong>
                                    <?php if ($match['score1'] !== null): ?>
                                        <span class="text-primary"><?= $match['score1'] ?> : <?= $match['score2'] ?></span>
                                    <?php else: ?>
                                        <span class="text-muted">vs</span>
                                    <?php endif; ?>
                                    <strong><?= htmlspecialchars($match['player2']) ?></strong>
                                </p>
                                <p><em><?= htmlspecialchars($match['status']) ?></em></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="bracket__round">
                        <h3>Półfinały</h3>
                        <?php foreach ($bracket['semifinals'] as $match): ?>
                            <div class="bracket__match bracket__match--pending">
                                <p>
                                    <strong><?= htmlspecialchars($match['player1']) ?></strong>
                                    <span class="text-muted">vs</span>
                                    <strong><?= htmlspecialchars($match['player2']) ?></strong>
                                </p>
                                <p><em><?= htmlspecialchars($match['status']) ?></em></p>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="bracket__round">
                        <h3>Finał</h3>
                        <?php foreach ($bracket['final'] as $match): ?>
                            <div class="bracket__match bracket__match--pending">
                                <p>
                                    <strong><?= htmlspecialchars($match['player1']) ?></strong>
                                    <span class="text-muted">vs</span>
                                    <strong><?= htmlspecialchars($match['player2']) ?></strong>
                                </p>
                                <p><em><?= htmlspecialchars($match['status']) ?></em></p>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </section>

            <section>
                <h2>Wpisz wynik meczu</h2>
                <form id="match-result-form" class="card">
                    <input type="hidden" name="tournament_id" value="<?= $tournament['id'] ?>">

                    <label for="match_id">Mecz (ID):</label>
                    <input type="number" id="match_id" name="match_id" required min="1">

                    <fieldset>
                        <legend>Wynik w legach</legend>
                        <label for="score1">Gracz 1 – legi:</label>
                        <input type="number" id="score1" name="score1" required min="0" max="10">

                        <label for="score2">Gracz 2 – legi:</label>
                        <input type="number" id="score2" name="score2" required min="0" max="10">
                    </fieldset>

                    <fieldset>
                        <legend>Statystyki gracz 1</legend>
                        <label for="avg1">Średnia 3 lotek:</label>
                        <input type="number" id="avg1" name="avg1" step="0.1" min="0" max="180">

                        <label for="max180_1">Liczba 180-tek:</label>
                        <input type="number" id="max180_1" name="max180_1" min="0">

                        <label for="checkout1">Najwyższy checkout:</label>
                        <input type="number" id="checkout1" name="checkout1" min="1" max="170">
                    </fieldset>

                    <fieldset>
                        <legend>Statystyki gracz 2</legend>
                        <label for="avg2">Średnia 3 lotek:</label>
                        <input type="number" id="avg2" name="avg2" step="0.1" min="0" max="180">

                        <label for="max180_2">Liczba 180-tek:</label>
                        <input type="number" id="max180_2" name="max180_2" min="0">

                        <label for="checkout2">Najwyższy checkout:</label>
                        <input type="number" id="checkout2" name="checkout2" min="1" max="170">
                    </fieldset>

                    <button type="submit">Zatwierdź wynik</button>
                    <p id="result-feedback" role="status"></p>
                </form>
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
