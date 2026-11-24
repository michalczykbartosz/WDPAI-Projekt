<article>
    <h1><?= htmlspecialchars($tournament->name) ?></h1>

    <section class="card mb-lg">
        <h2><?= $t('tournament.info') ?></h2>
        <dl>
            <dt><?= $t('tournament.format') ?></dt>
            <dd><?= htmlspecialchars($tournament->format()) ?></dd>

            <dt><?= $t('tournament.date') ?></dt>
            <dd><?= $tournament->startsAt->format('Y-m-d H:i') ?></dd>

            <dt><?= $t('tournament.location') ?></dt>
            <dd><?= htmlspecialchars($tournament->location) ?></dd>

            <dt><?= $t('tournament.status') ?></dt>
            <dd><span class="badge badge--status-<?= $tournament->status->value ?>"><?= $t('status.' . $tournament->status->value) ?></span></dd>

            <dt><?= $t('tournament.registered') ?></dt>
            <dd><span id="registered-count"><?= $tournament->registeredPlayers ?></span> / <?= $tournament->maxPlayers ?></dd>
        </dl>

        <?php if ($registrationState === 'open'): ?>
            <button type="button" id="btn-register" class="btn btn--primary"
                    data-url="/tournaments/<?= $tournament->id ?>/register"
                    data-label-loading="<?= $t('tournament.registering') ?>"
                    data-label-done="<?= $t('tournament.registered_self') ?>"
                    data-error-network="<?= $t('registration.error.network') ?>"
                    data-error-unknown="<?= $t('registration.error.unknown') ?>"><?= $t('tournament.register') ?></button>
        <?php elseif ($registrationState === 'already_registered'): ?>
            <button type="button" class="btn btn--primary" disabled><?= $t('tournament.registered_self') ?></button>
        <?php elseif ($registrationState === 'guest'): ?>
            <p><a href="/login"><?= $t('tournament.login_to_register') ?></a></p>
        <?php elseif ($registrationState !== null): ?>
            <p class="text-muted"><?= $t('registration.error.' . $registrationState) ?></p>
        <?php endif; ?>
        <p id="register-feedback" role="status"></p>
    </section>
</article>

<section>
    <h2><?= $t('tournament.players') ?></h2>
    <table id="players-table"<?= empty($players) ? ' hidden' : '' ?>>
        <thead>
            <tr>
                <th scope="col"><?= $t('stats.rank') ?></th>
                <th scope="col"><?= $t('stats.player') ?></th>
                <th scope="col"><?= $t('stats.avg3') ?></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($players as $p): ?>
                <tr>
                    <td class="rank-cell"><?= $p->seed ?? '–' ?></td>
                    <td><?= htmlspecialchars($p->displayName) ?></td>
                    <td><?= $p->threeDartAverage !== null ? number_format($p->threeDartAverage, 1) : '–' ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p id="players-empty"<?= empty($players) ? '' : ' hidden' ?>><?= $t('tournament.no_players') ?></p>
</section>

<section>
    <h2><?= $t('bracket.title') ?></h2>
    <?php if (!empty($rounds)): ?>
        <div class="bracket">
            <?php foreach ($rounds as $round): ?>
                <section class="bracket__round" aria-labelledby="round-<?= $round['number'] ?>">
                    <h3 id="round-<?= $round['number'] ?>"><?= htmlspecialchars($round['label']) ?></h3>
                    <?php foreach ($round['matches'] as $match): ?>
                        <?php require __DIR__ . '/partials/match-card.html.php'; ?>
                    <?php endforeach; ?>
                </section>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <p><?= $t('bracket.empty') ?></p>
    <?php endif; ?>
</section>

<section>
    <h2><?= $t('result.title') ?></h2>
    <form id="match-result-form" class="card">
        <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
        <input type="hidden" name="tournament_id" value="<?= $tournament->id ?>">

        <label for="match_id"><?= $t('result.match_id') ?></label>
        <input type="number" id="match_id" name="match_id" required min="1">

        <fieldset>
            <legend><?= $t('result.legs') ?></legend>
            <label for="score1"><?= $t('result.legs_player1') ?></label>
            <input type="number" id="score1" name="score1" required min="0" max="10">

            <label for="score2"><?= $t('result.legs_player2') ?></label>
            <input type="number" id="score2" name="score2" required min="0" max="10">
        </fieldset>

        <?php foreach ([1, 2] as $playerNumber): ?>
            <fieldset>
                <legend><?= $t('result.stats_player' . $playerNumber) ?></legend>
                <label for="avg<?= $playerNumber ?>"><?= $t('result.avg3') ?></label>
                <input type="number" id="avg<?= $playerNumber ?>" name="avg<?= $playerNumber ?>" step="0.1" min="0" max="180">

                <label for="max180_<?= $playerNumber ?>"><?= $t('result.180s') ?></label>
                <input type="number" id="max180_<?= $playerNumber ?>" name="max180_<?= $playerNumber ?>" min="0">

                <label for="checkout<?= $playerNumber ?>"><?= $t('result.checkout') ?></label>
                <input type="number" id="checkout<?= $playerNumber ?>" name="checkout<?= $playerNumber ?>" min="1" max="170">
            </fieldset>
        <?php endforeach; ?>

        <button type="submit"><?= $t('result.submit') ?></button>
        <p id="result-feedback" role="status"></p>
    </form>
</section>

<script type="module" src="/js/tournament-registration.js"></script>
