<?php
$fieldError = static function (string $field) use ($errors, $t): string {
    if (!isset($errors[$field])) {
        return '';
    }

    return '<p class="form-error" id="' . $field . '-error">' . $t($errors[$field]) . '</p>';
};

$invalidAttributes = static fn (string $field): string => isset($errors[$field])
    ? ' aria-invalid="true" aria-describedby="' . $field . '-error"'
    : '';

$selected = static fn (string $field, string $value): string => $old[$field] === $value ? ' selected' : '';
?>
<h1><?= $t('organizer.title') ?></h1>

<form action="/organizer/create" method="post" class="card" novalidate data-validate>
    <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>

    <fieldset>
        <legend><?= $t('organizer.section.basic') ?></legend>

        <label for="name"><?= $t('organizer.name') ?></label>
        <input type="text" id="name" name="name" required
               minlength="<?= $nameMinLength ?>" maxlength="<?= $nameMaxLength ?>"
               placeholder="<?= $t('organizer.name_placeholder') ?>"
               value="<?= htmlspecialchars($old['name']) ?>"<?= $invalidAttributes('name') ?>>
        <?= $fieldError('name') ?>

        <label for="description"><?= $t('organizer.description') ?></label>
        <textarea id="description" name="description" rows="4" maxlength="<?= $descriptionMaxLength ?>"
                  placeholder="<?= $t('organizer.description_placeholder') ?>"<?= $invalidAttributes('description') ?>><?= htmlspecialchars($old['description']) ?></textarea>
        <?= $fieldError('description') ?>
    </fieldset>

    <fieldset>
        <legend><?= $t('organizer.section.game') ?></legend>

        <label for="starting_score"><?= $t('organizer.starting_score') ?></label>
        <select id="starting_score" name="starting_score" required<?= $invalidAttributes('starting_score') ?>>
            <?php foreach ($startingScoreOptions as $score): ?>
                <option value="<?= (int) $score ?>"<?= $selected('starting_score', (string) $score) ?>><?= (int) $score ?></option>
            <?php endforeach; ?>
        </select>
        <?= $fieldError('starting_score') ?>

        <label for="finish_rule"><?= $t('organizer.finish_rule') ?></label>
        <select id="finish_rule" name="finish_rule" required<?= $invalidAttributes('finish_rule') ?>>
            <?php foreach ($finishRuleOptions as $rule): ?>
                <option value="<?= htmlspecialchars($rule->value) ?>"<?= $selected('finish_rule', $rule->value) ?>><?= htmlspecialchars($rule->label()) ?></option>
            <?php endforeach; ?>
        </select>
        <?= $fieldError('finish_rule') ?>

        <label for="max_players"><?= $t('organizer.max_players') ?></label>
        <select id="max_players" name="max_players" required<?= $invalidAttributes('max_players') ?>>
            <?php foreach ($maxPlayersOptions as $players): ?>
                <option value="<?= (int) $players ?>"<?= $selected('max_players', (string) $players) ?>><?= (int) $players ?></option>
            <?php endforeach; ?>
        </select>
        <?= $fieldError('max_players') ?>

        <label for="legs_to_win"><?= $t('organizer.legs_to_win') ?></label>
        <select id="legs_to_win" name="legs_to_win" required<?= $invalidAttributes('legs_to_win') ?>>
            <?php foreach ($legsToWinOptions as $legs): ?>
                <option value="<?= (int) $legs ?>"<?= $selected('legs_to_win', (string) $legs) ?>><?= (int) $legs ?></option>
            <?php endforeach; ?>
        </select>
        <?= $fieldError('legs_to_win') ?>
    </fieldset>

    <fieldset>
        <legend><?= $t('organizer.section.schedule') ?></legend>

        <label for="starts_at"><?= $t('organizer.starts_at') ?></label>
        <input type="datetime-local" id="starts_at" name="starts_at" required
               data-future="validation.tournament.starts_at_past"
               value="<?= htmlspecialchars($old['starts_at']) ?>"<?= $invalidAttributes('starts_at') ?>>
        <?= $fieldError('starts_at') ?>

        <label for="registration_deadline"><?= $t('organizer.deadline') ?></label>
        <input type="datetime-local" id="registration_deadline" name="registration_deadline" required
               data-future="validation.tournament.deadline_past"
               data-not-after="starts_at" data-not-after-message="validation.tournament.deadline_after_start"
               value="<?= htmlspecialchars($old['registration_deadline']) ?>"<?= $invalidAttributes('registration_deadline') ?>>
        <?= $fieldError('registration_deadline') ?>

        <label for="location"><?= $t('organizer.location') ?></label>
        <input type="text" id="location" name="location" required maxlength="<?= $locationMaxLength ?>"
               placeholder="<?= $t('organizer.location_placeholder') ?>"
               value="<?= htmlspecialchars($old['location']) ?>"<?= $invalidAttributes('location') ?>>
        <?= $fieldError('location') ?>
    </fieldset>

    <button type="submit"><?= $t('organizer.submit') ?></button>
</form>
