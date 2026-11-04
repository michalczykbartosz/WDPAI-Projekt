<h1>Globalny ranking graczy</h1>
<p>Ranking wyliczany na podstawie zagregowanych statystyk ze wszystkich turniejów.</p>

<?php if (!empty($players)): ?>
    <table>
        <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Zawodnik</th>
                <th scope="col">Mecze</th>
                <th scope="col">Wygrane</th>
                <th scope="col">% wygranych</th>
                <th scope="col">Średnia (3 lotki)</th>
                <th scope="col">180-ki</th>
                <th scope="col">Najlepszy checkout</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $p): ?>
                <tr>
                    <td class="rank-cell"><?= $p['rank'] ?></td>
                    <td>
                        <a href="/players/<?= (int) $p['id'] ?>">
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
