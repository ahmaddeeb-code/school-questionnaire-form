<?php
namespace App;

class Router
{
    private array $routes = [];

    public function get(string $path, callable $handler): void
    {
        $this->routes['GET'][] = [$path, $this->compile($path), $handler];
    }

    public function post(string $path, callable $handler): void
    {
        $this->routes['POST'][] = [$path, $this->compile($path), $handler];
    }

    private function compile(string $path): string
    {
        return '#^' . $path . '$#';
    }

    public function dispatch(string $method, string $uri): void
    {
        $path = rtrim(parse_url($uri, PHP_URL_PATH) ?? '/', '/') ?: '/';
        $routes = $this->routes[$method] ?? [];
        foreach ($routes as [$route, $pattern, $handler]) {
            if ($route === $path || preg_match($pattern, $path, $matches)) {
                $params = array_slice($matches ?? [], 1);
                $handler(...$params);
                return;
            }
        }
        http_response_code(404);
        echo '404 Not Found';
    }
}
