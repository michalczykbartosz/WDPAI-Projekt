<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LocalePreference;
use App\Service\Translator;

abstract class AppController
{
    private const VIEWS_DIR = __DIR__ . '/../../public/views/';
    private const LANG_DIR = __DIR__ . '/../../lang';

    /** Wartości domyślne dla wspólnego layoutu, nadpisywane przez kontrolery. */
    private const LAYOUT_DEFAULTS = [
        'activePage' => '',
        'userRole' => 'player',
    ];

    protected readonly Translator $translator;

    public function __construct()
    {
        $locale = (new LocalePreference())->current();
        $this->translator = new Translator($locale, self::LANG_DIR);
    }

    public function notFound(): void
    {
        http_response_code(404);

        $this->render('not-found', [
            'pageTitle' => $this->translator->get('title.not_found'),
            'suggestedLinks' => [
                ['url' => '/', 'label' => 'nav.home'],
                ['url' => '/tournaments', 'label' => 'nav.tournaments'],
                ['url' => '/leaderboard', 'label' => 'nav.leaderboard'],
            ],
        ]);
    }

    protected function render(string $template, array $variables = []): void
    {
        $variables = array_merge(self::LAYOUT_DEFAULTS, $variables);
        $variables['locale'] = $this->translator->locale();
        $variables['supportedLocales'] = Translator::SUPPORTED_LOCALES;
        $variables['currentPath'] = $_SERVER['REQUEST_URI'] ?? '/';
        $variables['t'] = $this->createViewTranslator();
        $variables['content'] = $this->renderTemplate($template, $variables);

        echo $this->renderTemplate('layout', $variables);
    }

    /**
     * Widoki dostają funkcję $t(), która od razu koduje tekst do HTML,
     * więc tłumaczenia i podstawiane parametry są bezpieczne przy wypisywaniu.
     */
    private function createViewTranslator(): \Closure
    {
        return fn (string $key, array $parameters = []): string => htmlspecialchars(
            $this->translator->get($key, $parameters),
            ENT_QUOTES,
            'UTF-8',
        );
    }

    private function renderTemplate(string $template, array $variables): string
    {
        $templatePath = self::VIEWS_DIR . $template . '.html.php';

        if (!is_file($templatePath)) {
            throw new \RuntimeException('View not found: ' . $template);
        }

        extract($variables, EXTR_SKIP);

        ob_start();

        try {
            require $templatePath;
        } catch (\Throwable $exception) {
            ob_end_clean();
            throw $exception;
        }

        return (string) ob_get_clean();
    }
}
