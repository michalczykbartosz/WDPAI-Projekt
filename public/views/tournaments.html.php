<?php
$formatFilters = [
    '501do' => '501 Double Out',
    '301do' => '301 Double Out',
    '501so' => '501 Single Out',
];

$statusFilters = ['open', 'ongoing', 'finished'];
?>
<h1><?= $t('tournaments.title') ?></h1>

<section>
    <form method="get" action="/tournaments" class="filter-bar">
        <div>
            <label for="format"><?= $t('filter.format') ?></label>
            <select name="format" id="format">
                <option value=""><?= $t('filter.all') ?></option>
                <?php foreach ($formatFilters as $value => $label): ?>
                    <option value="<?= htmlspecialchars($value) ?>"><?= htmlspecialchars($label) ?></option>
                <?php endforeach; ?>
            </select>
        </div>

        <div>
            <label for="status"><?= $t('filter.status') ?></label>
            <select name="status" id="status">
                <option value=""><?= $t('filter.all') ?></option>
                <?php foreach ($statusFilters as $status): ?>
                    <option value="<?= htmlspecialchars($status) ?>"><?= $t('status.' . $status) ?></option>
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
                            <a href="/tournaments/<?= (int) $tournament['id'] ?>">
                                <?= htmlspecialchars($tournament['name']) ?>
                            </a>
                        </td>
                        <td><?= htmlspecialchars($tournament['format']) ?></td>
                        <td><?= htmlspecialchars($tournament['date']) ?></td>
                        <td><?= htmlspecialchars($tournament['location']) ?></td>
                        <td><?= (int) $tournament['registeredPlayers'] ?> / <?= (int) $tournament['maxPlayers'] ?></td>
                        <td>
                            <span class="badge badge--status-<?= htmlspecialchars($tournament['status']) ?>"><?= $t('status.' . $tournament['status']) ?></span>
                        </td>
                        <td>
                            <a href="/tournaments/<?= (int) $tournament['id'] ?>" class="btn btn--secondary"><?= $t('tournaments.details') ?></a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p><?= $t('tournaments.empty') ?></p>
    <?php endif; ?>
</section>
