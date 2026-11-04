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
                    <th scope="col">#</th>
                    <th scope="col">Zawodnik</th>
                    <th scope="col">Średnia (3 lotki)</th>
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
    <?php
    $roundLabels = [
        'quarterfinals' => 'Ćwierćfinały',
        'semifinals' => 'Półfinały',
        'final' => 'Finał',
    ];
    ?>
    <div class="bracket">
        <?php foreach ($bracket as $roundKey => $matches): ?>
            <section class="bracket__round" aria-labelledby="round-<?= htmlspecialchars($roundKey) ?>">
                <h3 id="round-<?= htmlspecialchars($roundKey) ?>"><?= htmlspecialchars($roundLabels[$roundKey] ?? $roundKey) ?></h3>
                <?php foreach ($matches as $match): ?>
                    <?php require __DIR__ . '/partials/match-card.html.php'; ?>
                <?php endforeach; ?>
            </section>
        <?php endforeach; ?>
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
