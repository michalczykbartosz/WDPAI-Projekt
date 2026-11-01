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
        <article>
            <h1>Profil zawodnika: <?= htmlspecialchars($player['name']) ?></h1>

            <section>
                <h2>Dane zawodnika</h2>

                <figure>
                    <?php if ($player['avatar']): ?>
                        <img src="<?= htmlspecialchars($player['avatar']) ?>"
                             alt="Awatar <?= htmlspecialchars($player['name']) ?>"
                             width="150" height="150">
                    <?php else: ?>
                        <p><em>Brak zdjęcia profilowego</em></p>
                    <?php endif; ?>
                </figure>

                <dl>
                    <dt>Imię i nazwisko</dt>
                    <dd><?= htmlspecialchars($player['name']) ?></dd>

                    <dt>Członek od</dt>
                    <dd><?= htmlspecialchars($player['joinedDate']) ?></dd>
                </dl>

                <!-- Upload awatara -->
                <form action="/players/<?= $player['id'] ?>/avatar" method="post" enctype="multipart/form-data">
                    <label for="avatar">Zmień zdjęcie profilowe:</label>
                    <input type="file" id="avatar" name="avatar" accept="image/jpeg,image/png,image/webp">
                    <button type="submit">Prześlij</button>
                </form>
            </section>

            <section>
                <h2>Statystyki kariery</h2>
                <dl>
                    <dt>Mecze rozegrane</dt>
                    <dd><?= $player['matchesPlayed'] ?></dd>

                    <dt>Wygrane</dt>
                    <dd><?= $player['wins'] ?></dd>

                    <dt>Przegrane</dt>
                    <dd><?= $player['losses'] ?></dd>

                    <dt>Procent wygranych</dt>
                    <dd><?= number_format($player['winRate'], 1) ?>%</dd>

                    <dt>Średnia 3 lotek</dt>
                    <dd><?= number_format($player['avg3dart'], 1) ?></dd>

                    <dt>Suma 180-tek</dt>
                    <dd><?= $player['total180s'] ?></dd>

                    <dt>Rekordowy checkout</dt>
                    <dd><?= $player['bestCheckout'] ?></dd>
                </dl>
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
                                <td><?= htmlspecialchars($m['result']) ?></td>
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
    </main>

    <footer>
        <p>&copy; 2026 DartTournament. Projekt WDPAI.</p>
    </footer>
</body>
</html>
