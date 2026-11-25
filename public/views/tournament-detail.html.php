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
    <?php if ($canGenerateBracket): ?>
        <form action="/tournaments/<?= $tournament->id ?>/bracket" method="post" class="card mb-lg">
            <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>
            <p class="text-muted"><?= $t('bracket.generate_hint') ?></p>
            <button type="submit" class="btn btn--primary"><?= $t('bracket.generate') ?></button>
        </form>
    <?php endif; ?>
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

<?php if ($tournament->status === \App\Model\TournamentStatus::Ongoing): ?>
    <dialog id="result-dialog" class="result-dialog" aria-labelledby="result-dialog-title"
            data-label-saving="<?= $t('result.saving') ?>"
            data-error-network="<?= $t('result.error.network') ?>"
            data-error-unknown="<?= $t('result.error.unknown') ?>">
        <form id="result-form" novalidate>
            <h2 id="result-dialog-title"><?= $t('result.title') ?></h2>
            <p class="text-muted"><?= $t('result.legs_to_win', ['legs' => $tournament->legsToWin]) ?></p>
            <p class="form-error" id="result-error" role="alert" tabindex="-1" hidden></p>

            <?php foreach ([1, 2] as $player): ?>
                <fieldset>
                    <legend data-player-name="<?= $player ?>"></legend>

                    <label for="score<?= $player ?>"><?= $t('result.legs') ?></label>
                    <select id="score<?= $player ?>" name="score<?= $player ?>" required>
                        <option value="">—</option>
                        <?php for ($legs = 0; $legs <= $tournament->legsToWin; $legs++): ?>
                            <option value="<?= $legs ?>"><?= $legs ?></option>
                        <?php endfor; ?>
                    </select>

                    <label for="avg<?= $player ?>"><?= $t('result.avg3') ?></label>
                    <input type="text" inputmode="decimal" id="avg<?= $player ?>" name="avg<?= $player ?>" required
                           autocomplete="off" placeholder="<?= $t('result.avg_placeholder') ?>">

                    <label for="max180_<?= $player ?>"><?= $t('result.180s') ?></label>
                    <input type="number" id="max180_<?= $player ?>" name="max180_<?= $player ?>" min="0" max="99" value="0">

                    <label for="checkout<?= $player ?>"><?= $t('result.checkout') ?></label>
                    <input type="number" id="checkout<?= $player ?>" name="checkout<?= $player ?>"
                           min="<?= $tournament->finishRule->minCheckout() ?>" max="<?= $tournament->finishRule->maxCheckout() ?>"
                           aria-describedby="checkout<?= $player ?>-hint">
                    <p class="text-muted form-hint" id="checkout<?= $player ?>-hint"><?= $t('result.checkout_hint') ?></p>
                </fieldset>
            <?php endforeach; ?>

            <div class="dialog__actions">
                <button type="button" class="btn btn--secondary" data-close><?= $t('result.cancel') ?></button>
                <button type="submit" class="btn btn--primary"><?= $t('result.submit') ?></button>
            </div>
        </form>
    </dialog>
    <script type="module" src="/js/match-result.js"></script>
<?php endif; ?>

<script type="module" src="/js/tournament-registration.js"></script>
