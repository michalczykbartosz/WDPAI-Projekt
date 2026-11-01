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
                            <td><?= $p['rank'] ?></td>
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
    </main>

    <footer>
        <p>&copy; 2026 DartTournament. Projekt WDPAI.</p>
    </footer>
</body>
</html>
