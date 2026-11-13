<div class="error-page">
    <h1><?= $statusCode ?></h1>
    <p><?= $t($errorKey . '.message') ?></p>

    <?php if (!empty($suggestedLinks)): ?>
        <nav>
            <h2><?= $t('error.suggestions') ?></h2>
            <ul>
                <?php foreach ($suggestedLinks as $link): ?>
                    <li>
                        <a href="<?= htmlspecialchars($link['url']) ?>">
                            <?= $t($link['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
