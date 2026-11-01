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
        <h1>Panel organizatora – Utwórz nowy turniej</h1>

        <form action="/organizer/create" method="post">
            <fieldset>
                <legend>Podstawowe informacje</legend>

                <label for="name">Nazwa turnieju:</label>
                <input type="text" id="name" name="name" required
                       placeholder="np. Puchar Krakowa 2026" maxlength="100">

                <label for="description">Opis:</label>
                <textarea id="description" name="description" rows="4"
                          placeholder="Krótki opis turnieju..."></textarea>
            </fieldset>

            <fieldset>
                <legend>Parametry rozgrywki</legend>

                <label for="format">Format gry:</label>
                <select id="format" name="format" required>
                    <option value="">— wybierz —</option>
                    <?php foreach ($formats as $f): ?>
                        <option value="<?= htmlspecialchars($f) ?>">
                            <?= htmlspecialchars($f) ?>
                        </option>
                    <?php endforeach; ?>
                </select>

                <label for="max_players">Maks. liczba graczy:</label>
                <select id="max_players" name="max_players" required>
                    <?php foreach ($maxPlayersOptions as $opt): ?>
                        <option value="<?= $opt ?>"><?= $opt ?></option>
                    <?php endforeach; ?>
                </select>

                <label for="legs_to_win">Legi do wygrania meczu:</label>
                <input type="number" id="legs_to_win" name="legs_to_win"
                       value="3" min="1" max="10" required>
            </fieldset>

            <fieldset>
                <legend>Termin i lokalizacja</legend>

                <label for="date">Data turnieju:</label>
                <input type="date" id="date" name="date" required>

                <label for="registration_deadline">Zamknięcie zapisów:</label>
                <input type="datetime-local" id="registration_deadline"
                       name="registration_deadline" required>

                <label for="location">Lokalizacja:</label>
                <input type="text" id="location" name="location" required
                       placeholder="np. Kraków, Dart Arena" maxlength="150">
            </fieldset>

            <button type="submit">Utwórz turniej</button>
        </form>
    </main>

    <footer>
        <p>&copy; 2026 DartTournament. Projekt WDPAI.</p>
    </footer>
</body>
</html>
