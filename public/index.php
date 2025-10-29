<?php
use App\Router;
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

require __DIR__ . '/../vendor/autoload.php';

$config = require __DIR__ . '/../config.php';

session_start();

$dsn = sprintf('mysql:host=%s;port=%d;dbname=%s;charset=%s', $config['db']['host'], $config['db']['port'], $config['db']['database'], $config['db']['charset']);
$pdo = new PDO($dsn, $config['db']['username'], $config['db']['password'], [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
]);

Auth::setConnection($pdo);
I18n::init($_SESSION['locale'] ?? $config['app']['locale_default'], $config['app']['locales'], $config['app']['locale_default']);

$router = new Router();

$authController = new AuthController($pdo);
$dashboardController = new DashboardController($pdo);
$formController = new FormController($pdo);
$builderController = new BuilderController($pdo);
$assignController = new AssignController($pdo);
$responseController = new ResponseController($pdo);
$analyticsController = new AnalyticsController($pdo);
$exportController = new ExportController($pdo);
$localeController = new LocaleController($pdo);

$router->get('/', fn() => header('Location: /login'));
$router->get('/login', fn() => $authController->showLogin());
$router->post('/login', fn() => $authController->login());
$router->get('/logout', fn() => $authController->logout());

$router->get('/locale/en', fn() => $localeController->switch('en'));
$router->get('/locale/ar', fn() => $localeController->switch('ar'));

$router->get('/dashboard', function () use ($dashboardController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($dashboardController) {
        $dashboardController->index();
    });
});

$router->get('/forms', function () use ($formController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($formController) {
        $formController->index();
    });
});
$router->get('/forms/create', function () use ($formController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($formController) {
        $formController->create();
    });
});
$router->post('/forms', function () use ($formController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($formController) {
        $formController->store();
    });
});
$router->get('/forms/(\d+)/edit', function ($id) use ($formController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($formController, $id) {
        $formController->edit((int)$id);
    });
});
$router->post('/forms/(\d+)', function ($id) use ($formController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($formController, $id) {
        $formController->update((int)$id);
    });
});

$router->get('/forms/(\d+)/builder', function ($id) use ($builderController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($builderController, $id) {
        $builderController->builder((int)$id);
    });
});
$router->post('/forms/(\d+)/questions', function ($id) use ($builderController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($builderController, $id) {
        $builderController->addQuestion((int)$id);
    });
});
$router->post('/forms/(\d+)/reorder', function ($id) use ($builderController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($builderController, $id) {
        $builderController->reorder((int)$id);
    });
});

$router->get('/forms/(\d+)/assign', function ($id) use ($assignController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($assignController, $id) {
        $assignController->show((int)$id);
    });
});
$router->post('/forms/(\d+)/assign', function ($id) use ($assignController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($assignController, $id) {
        $assignController->assign((int)$id);
    });
});

$router->get('/forms/(\d+)/analytics', function ($id) use ($analyticsController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($analyticsController, $id) {
        $analyticsController->show((int)$id);
    });
});
$router->get('/forms/(\d+)/export/responses', function ($id) use ($exportController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($exportController, $id) {
        $exportController->exportResponses((int)$id);
    });
});
$router->get('/forms/(\d+)/export/analytics', function ($id) use ($exportController) {
    AuthMiddleware::requireLogin(['admin', 'employee'])(function () use ($exportController, $id) {
        $exportController->exportAnalytics((int)$id);
    });
});

$router->get('/respond/([A-Za-z0-9]+)', function ($token) use ($responseController) {
    $responseController->show($token);
});
$router->post('/respond/([A-Za-z0-9]+)', function ($token) use ($responseController) {
    $responseController->submit($token);
});

$router->dispatch($_SERVER['REQUEST_METHOD'], $_SERVER['REQUEST_URI']);
