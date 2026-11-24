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
?>
<h1><?= $t('register.title') ?></h1>

<form action="/register" method="post" class="card form-narrow" novalidate data-validate>
    <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>

    <label for="email"><?= $t('register.email') ?></label>
    <input type="email" id="email" name="email" required maxlength="255" autocomplete="email"
           value="<?= htmlspecialchars($old['email']) ?>"<?= $invalidAttributes('email') ?>>
    <?= $fieldError('email') ?>

    <label for="display_name"><?= $t('register.display_name') ?></label>
    <input type="text" id="display_name" name="display_name" required autocomplete="nickname"
           minlength="<?= $displayNameMinLength ?>" maxlength="<?= $displayNameMaxLength ?>"
           value="<?= htmlspecialchars($old['display_name']) ?>"<?= $invalidAttributes('display_name') ?>>
    <?= $fieldError('display_name') ?>

    <label for="password"><?= $t('register.password', ['min' => $passwordMinLength]) ?></label>
    <input type="password" id="password" name="password" required autocomplete="new-password"
           minlength="<?= $passwordMinLength ?>"<?= $invalidAttributes('password') ?>>
    <?= $fieldError('password') ?>

    <label for="password_confirmation"><?= $t('register.password_confirmation') ?></label>
    <input type="password" id="password_confirmation" name="password_confirmation" required autocomplete="new-password"
           data-match="password" data-match-message="validation.password.mismatch"<?= $invalidAttributes('password_confirmation') ?>>
    <?= $fieldError('password_confirmation') ?>

    <button type="submit"><?= $t('register.submit') ?></button>
</form>
