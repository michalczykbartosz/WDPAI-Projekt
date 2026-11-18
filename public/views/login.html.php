<h1><?= $t('login.title') ?></h1>

<form action="/login" method="post" class="card form-narrow" novalidate>
    <?php require __DIR__ . '/partials/csrf-field.html.php'; ?>

    <?php if ($error !== null): ?>
        <p class="form-error" id="login-error" role="alert"><?= $t($error) ?></p>
    <?php endif; ?>

    <label for="email"><?= $t('login.email') ?></label>
    <input type="email" id="email" name="email" required autocomplete="email"
           value="<?= htmlspecialchars($oldEmail) ?>"<?= $error !== null ? ' aria-invalid="true" aria-describedby="login-error"' : '' ?>>

    <label for="password"><?= $t('login.password') ?></label>
    <input type="password" id="password" name="password" required autocomplete="current-password"<?= $error !== null ? ' aria-invalid="true" aria-describedby="login-error"' : '' ?>>

    <button type="submit"><?= $t('login.submit') ?></button>

    <p class="mt-md"><?= $t('login.no_account') ?> <a href="/register"><?= $t('nav.register') ?></a></p>
</form>
