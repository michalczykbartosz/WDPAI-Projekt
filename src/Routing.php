<?php

declare(strict_types=1);

namespace App;



final class Routing
{
    private array $routes = [];

    public function __construct(private readonly Container $container)
    {
    }

    public function get(string $path, string $controller, string $action): void
    {
        $this->add('GET', $path, $controller, $action);
    }

    public function post(string $path, string $controller, string $action): void
    {
        $this->add('POST', $path, $controller, $action);
    }

    private function add(
        string $method,
        string $path,
        string $controller,
        string $action,
    ): void {
        $this->routes[] = [
            'method' => $method,
            'pattern' => $this->createPattern($path),
            'controller' => $controller,
            'action' => $action,
        ];
    }

	/* 
	Funkcja dzieli ścieżkę na fragmenty. Zwykłe fragmenty pozostawia bez zmian, 
	a fragment zapisany w klamrach, na przykład {id}, zamienia na nazwany 
	parametr przyjmujący dowolny tekst bez ukośnika. Format wartości sprawdza kontroler,
	dzięki czemu może odpowiedzieć 400 (zły format) albo 404 (brak rekordu).
	*/
    private function createPattern(string $path): string
    {
        $parts = explode('/', $path);

        foreach ($parts as &$part) {
            if (str_starts_with($part, '{') && str_ends_with($part, '}')) {
                $parameterName = trim($part, '{}');
                $part = '(?P<' . $parameterName . '>[^/]+)';
                continue;
            }

            $part = preg_quote($part, '~');
        }
        unset($part);

        return '~^' . implode('/', $parts) . '$~';
    }

    public function dispatch(string $method, string $uri): bool
    {
        $path = parse_url($uri, PHP_URL_PATH) ?: '/';

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            if (!preg_match($route['pattern'], $path, $matches)) {
                continue;
            }

            $parameters = array_filter(
                $matches,
                static fn (int|string $key): bool => is_string($key),
                ARRAY_FILTER_USE_KEY,
            );

            $controller = $this->container->controller($route['controller']);
            $controller->{$route['action']}(...$parameters);

            return true;
        }

        return false;
    }
}