<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Csrf;
use App\Helpers\Auth;
use App\Helpers\I18n;
use PDO;

class AuthController extends BaseController
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    public function showLogin(): void
    {
        $this->view('auth/login', [
            'title' => I18n::translate('login.title'),
            'csrf' => Csrf::field(),
        ]);
    }

    public function login(): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? '')) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return;
        }
        $email = $_POST['email'] ?? '';
        $password = $_POST['password'] ?? '';
        if (Auth::attempt($email, $password)) {
            header('Location: /dashboard');
            exit;
        }
        $this->view('auth/login', [
            'title' => I18n::translate('login.title'),
            'error' => I18n::translate('login.failed'),
            'csrf' => Csrf::field(),
        ]);
    }

    public function logout(): void
    {
        Auth::logout();
        header('Location: /login');
        exit;
    }
}
