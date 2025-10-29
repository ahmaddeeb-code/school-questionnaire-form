<?php
spl_autoload_register(function (string $class): void {
    $prefix = 'App\\';
    if (strpos($class, $prefix) !== 0) {
        return;
    }
    $relative = substr($class, strlen($prefix));
    $segments = explode('\\', $relative);
    if ($segments) {
        $segments[0] = strtolower($segments[0]);
    }
    $path = __DIR__ . '/' . implode('/', $segments) . '.php';
    if (file_exists($path)) {
        require $path;
    }
});
