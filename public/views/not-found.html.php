<div class="error-page">
    <h1>404</h1>
    <p>Przepraszamy, szukana strona nie istnieje lub została usunięta.</p>

    <?php if (!empty($suggestedLinks)): ?>
        <nav>
            <h2>Może szukasz:</h2>
            <ul>
                <?php foreach ($suggestedLinks as $link): ?>
                    <li>
                        <a href="<?= htmlspecialchars($link['url']) ?>">
                            <?= htmlspecialchars($link['label']) ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>
