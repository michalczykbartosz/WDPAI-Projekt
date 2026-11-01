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
        <h1>Eksplorator turniejów</h1>

        <section>
            <h2>Filtruj turnieje</h2>
            <form method="get" action="/tournaments">
                <label for="format">Format:</label>
                <select name="format" id="format">
                    <option value="">Wszystkie</option>
                    <option value="501do">501 Double Out</option>
                    <option value="301do">301 Double Out</option>
                    <option value="501so">501 Single Out</option>
                </select>

                <label for="status">Status:</label>
                <select name="status" id="status">
                    <option value="">Wszystkie</option>
                    <option value="open">Zapisy otwarte</option>
                    <option value="ongoing">W trakcie</option>
                    <option value="finished">Zakończony</option>
                </select>

                <button type="submit">Filtruj</button>
            </form>
        </section>

        <section>
            <h2>Lista turniejów</h2>
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
                                <td><?= htmlspecialchars($t['status']) ?></td>
                                <td>
                                    <a href="/tournaments/<?= $t['id'] ?>">Szczegóły</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Brak turniejów spełniających kryteria.</p>
            <?php endif; ?>
        </section>
    </main>

    <footer>
        <p>&copy; 2026 DartTournament. Projekt WDPAI.</p>
    </footer>
</body>
</html>
