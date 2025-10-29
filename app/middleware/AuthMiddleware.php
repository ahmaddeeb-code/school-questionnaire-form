<?php
namespace App\Middleware;

use App\Helpers\Auth;

class AuthMiddleware
{
    public static function requireLogin(array $roles = []): callable
    {
        return function (callable $next) use ($roles) {
            if (!Auth::check()) {
                header('Location: /login');
                exit;
            }
            if ($roles && !Auth::userHasRole($roles)) {
                http_response_code(403);
                echo 'Forbidden';
                return;
            }
            $next();
        };
    }
}
