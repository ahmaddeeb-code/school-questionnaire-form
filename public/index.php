<?php
use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\FormController;
use App\Controllers\BuilderController;
use App\Controllers\AssignController;
use App\Controllers\ResponseController;
use App\Controllers\AnalyticsController;
use App\Controllers\ExportController;
use App\Controllers\LocaleController;
use App\Middleware\AuthMiddleware;
use App\Helpers\Auth;
use App\Helpers\I18n;

require __DIR__ . '/../app/autoload.php';

$config = require __DIR__ . '/../config.php';

session_start();

$dsn = sprintf(
    'mysql:host=%s;port=%d;dbname=%s;charset=%s',
    $config['db']['host'],
    $config['db']['port'],
    $config['db']['database'],
    $config['db']['charset']
);
$pdo = new PDO($dsn, $config['db']['username'], $config['db']['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

Auth::setConnection($pdo);
I18n::init(
    $_SESSION['locale'] ?? $config['app']['locale_default'],
    $config['app']['locales'],
    $config['app']['locale_default']
);

$authController = new AuthController($pdo);
$dashboardController = new DashboardController($pdo);
$formController = new FormController($pdo);
$builderController = new BuilderController($pdo);
$assignController = new AssignController($pdo);
$responseController = new ResponseController($pdo);
$analyticsController = new AnalyticsController($pdo);
$exportController = new ExportController($pdo);
$localeController = new LocaleController($pdo);

$method = $_SERVER['REQUEST_METHOD'];
$path = rtrim(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/', '/') ?: '/';

$handled = true;

if ($method === 'GET' && $path === '/') {
    header('Location: /login');
    exit;
} elseif ($method === 'GET' && $path === '/login') {
    $authController->showLogin();
} elseif ($method === 'POST' && $path === '/login') {
    $authController->login();
} elseif ($method === 'GET' && $path === '/logout') {
    $authController->logout();
} elseif ($method === 'GET' && $path === '/locale/en') {
    $localeController->switch('en');
} elseif ($method === 'GET' && $path === '/locale/ar') {
    $localeController->switch('ar');
} elseif ($method === 'GET' && $path === '/dashboard') {
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $dashboardController->index());
} elseif ($method === 'GET' && $path === '/forms') {
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $formController->index());
} elseif ($method === 'GET' && $path === '/forms/create') {
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $formController->create());
} elseif ($method === 'POST' && $path === '/forms') {
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $formController->store());
} elseif ($method === 'GET' && preg_match('#^/forms/(\d+)/edit$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $formController->edit($formId));
} elseif ($method === 'POST' && preg_match('#^/forms/(\d+)$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $formController->update($formId));
} elseif ($method === 'GET' && preg_match('#^/forms/(\d+)/builder$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $builderController->builder($formId));
} elseif ($method === 'POST' && preg_match('#^/forms/(\d+)/questions$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $builderController->addQuestion($formId));
} elseif ($method === 'POST' && preg_match('#^/forms/(\d+)/reorder$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $builderController->reorder($formId));
} elseif ($method === 'GET' && preg_match('#^/forms/(\d+)/assign$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $assignController->show($formId));
} elseif ($method === 'POST' && preg_match('#^/forms/(\d+)/assign$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $assignController->assign($formId));
} elseif ($method === 'GET' && preg_match('#^/forms/(\d+)/analytics$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $analyticsController->show($formId));
} elseif ($method === 'GET' && preg_match('#^/forms/(\d+)/export/responses$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $exportController->exportResponses($formId));
} elseif ($method === 'GET' && preg_match('#^/forms/(\d+)/export/analytics$#', $path, $matches)) {
    $formId = (int)$matches[1];
    $guard = AuthMiddleware::requireLogin(['admin', 'employee']);
    $guard(fn() => $exportController->exportAnalytics($formId));
} elseif ($method === 'GET' && preg_match('#^/respond/([A-Za-z0-9]+)$#', $path, $matches)) {
    $responseController->show($matches[1]);
} elseif ($method === 'POST' && preg_match('#^/respond/([A-Za-z0-9]+)$#', $path, $matches)) {
    $responseController->submit($matches[1]);
} else {
    $handled = false;
}

if (!$handled) {
    http_response_code(404);
    echo '404 Not Found';
}
