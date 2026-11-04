<h1><?= $t('organizer.title') ?></h1>

<form action="/organizer/create" method="post" class="card">
    <fieldset>
        <legend><?= $t('organizer.section.basic') ?></legend>

        <label for="name"><?= $t('organizer.name') ?></label>
        <input type="text" id="name" name="name" required
               placeholder="<?= $t('organizer.name_placeholder') ?>" maxlength="100">

        <label for="description"><?= $t('organizer.description') ?></label>
        <textarea id="description" name="description" rows="4"
                  placeholder="<?= $t('organizer.description_placeholder') ?>"></textarea>
    </fieldset>

    <fieldset>
        <legend><?= $t('organizer.section.game') ?></legend>

        <label for="format"><?= $t('organizer.format') ?></label>
        <select id="format" name="format" required>
            <option value=""><?= $t('organizer.choose') ?></option>
            <?php foreach ($formats as $f): ?>
                <option value="<?= htmlspecialchars($f) ?>">
                    <?= htmlspecialchars($f) ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="max_players"><?= $t('organizer.max_players') ?></label>
        <select id="max_players" name="max_players" required>
            <?php foreach ($maxPlayersOptions as $opt): ?>
                <option value="<?= (int) $opt ?>"><?= (int) $opt ?></option>
            <?php endforeach; ?>
        </select>

        <label for="legs_to_win"><?= $t('organizer.legs_to_win') ?></label>
        <input type="number" id="legs_to_win" name="legs_to_win"
               value="3" min="1" max="10" required>
    </fieldset>

    <fieldset>
        <legend><?= $t('organizer.section.schedule') ?></legend>

        <label for="date"><?= $t('organizer.date') ?></label>
        <input type="date" id="date" name="date" required>

        <label for="registration_deadline"><?= $t('organizer.deadline') ?></label>
        <input type="datetime-local" id="registration_deadline"
               name="registration_deadline" required>

        <label for="location"><?= $t('organizer.location') ?></label>
        <input type="text" id="location" name="location" required
               placeholder="<?= $t('organizer.location_placeholder') ?>" maxlength="150">
    </fieldset>

    <button type="submit"><?= $t('organizer.submit') ?></button>
</form>
