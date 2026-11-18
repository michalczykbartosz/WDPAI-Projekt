<?php

declare(strict_types=1);

namespace App\Controller;

use App\Service\LocalePreference;
use App\Service\Translator;

final class LanguageController extends AppController
{
    public function change(): void
    {
        $locale = $_POST['locale'] ?? '';

        if (!is_string($locale) || !Translator::isSupported($locale)) {
            $this->badRequest();
            return;
        }

        (new LocalePreference($this->session))->change($locale);

        $this->redirect($this->safeRedirectTarget($_POST['redirect'] ?? null));
    }

    /**
     * Przekierowanie tylko na ścieżkę w obrębie aplikacji, żeby formularza
     * nie dało się użyć do odesłania użytkownika na obcą stronę (open redirect).
     */
    private function safeRedirectTarget(mixed $target): string
    {
        if (
            !is_string($target)
            || !str_starts_with($target, '/')
            || str_starts_with($target, '//')
            || str_contains($target, '\\')
        ) {
            return '/';
        }

        return $target;
    }
}
