<!doctype html>
<html lang="<?= htmlspecialchars($locale) ?>" data-theme="dark">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle) ?></title>
    <script>
        // Ustawia motyw przed pierwszym renderowaniem strony, żeby uniknąć mignięcia (FOUC).
        (function () {
            let theme = null;
            try {
                theme = localStorage.getItem('dartarena-theme');
            } catch (e) {}
            if (theme !== 'light' && theme !== 'dark') {
                theme = window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark';
            }
            document.documentElement.setAttribute('data-theme', theme);
        })();
    </script>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="/css/style.css">
</head>
<body>
    <?php require __DIR__ . '/partials/header.html.php'; ?>

    <main>
        <div class="container">
<?= $content ?>
        </div>
    </main>

    <?php require __DIR__ . '/partials/footer.html.php'; ?>

    <script src="/js/theme.js"></script>
    <script src="/js/navigation.js"></script>
</body>
</html>
