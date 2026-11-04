<div class="error-page">
    <h1>404</h1>
    <p><?= $t('not_found.message') ?></p>

    <?php if (!empty($suggestedLinks)): ?>
        <nav>
            <h2><?= $t('not_found.suggestions') ?></h2>
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
