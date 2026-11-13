<h1><?= $t('tournaments.title') ?></h1>

<section>
    <form method="get" action="/tournaments" class="filter-bar">
        <div>
            <label for="format"><?= $t('filter.format') ?></label>
            <select name="format" id="format">
                <option value=""><?= $t('filter.all') ?></option>
                <?php foreach ($formatOptions as $startingScore): ?>
                    <option value="<?= $startingScore ?>"<?= (string) $startingScore === $selectedFormat ? ' selected' : '' ?>><?= $startingScore ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="status"><?= $t('filter.status') ?></label>
            <select name="status" id="status">
                <option value=""><?= $t('filter.all') ?></option>
                <?php foreach ($statusOptions as $status): ?>
                    <option value="<?= $status->value ?>"<?= $status->value === $selectedStatus ? ' selected' : '' ?>><?= $t('status.' . $status->value) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <button type="submit" class="btn btn--primary"><?= $t('filter.submit') ?></button>
    </form>
</section>

<section>
    <?php if (!empty($tournaments)): ?>
        <table>
            <thead>
                <tr>
                    <th scope="col"><?= $t('tournaments.column.name') ?></th>
                    <th scope="col"><?= $t('tournaments.column.format') ?></th>
                    <th scope="col"><?= $t('tournaments.column.date') ?></th>
                    <th scope="col"><?= $t('tournaments.column.location') ?></th>
                    <th scope="col"><?= $t('tournaments.column.registrations') ?></th>
                    <th scope="col"><?= $t('tournaments.column.status') ?></th>
                    <th scope="col"><?= $t('tournaments.column.action') ?></th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($tournaments as $tournament): ?>
                    <tr>
                        <td>
                            <a href="/tournaments/<?= $tournament->id ?>">
                                <?= htmlspecialchars($tournament->name) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($tournament->format()) ?></td>
                        <td><?= $tournament->startsAt->format('Y-m-d') ?></td>
                        <td><?= htmlspecialchars($tournament->location) ?></td>
                        <td><?= $tournament->registeredPlayers ?> / <?= $tournament->maxPlayers ?></td>
                        <td>
                            <span class="badge badge--status-<?= $tournament->status->value ?>"><?= $t('status.' . $tournament->status->value) ?></span>
                        </td>
                        <td>
                            <a href="/tournaments/<?= $tournament->id ?>" class="btn btn--secondary"><?= $t('tournaments.details') ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?= $t('tournaments.empty') ?></p>
    <?php endif; ?>
</section>
