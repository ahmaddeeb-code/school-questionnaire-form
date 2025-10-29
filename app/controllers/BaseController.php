<?php
namespace App\Controllers;

use PDO;

class BaseController
{
    protected PDO $pdo;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    protected function view(string $template, array $data = []): void
    {
        $viewFile = $template;
        extract($data);
        include __DIR__ . '/../views/layout.php';
    }

    protected function renderPartial(string $template, array $data = []): void
    {
        extract($data);
        include __DIR__ . '/../views/' . $template . '.php';
    }
}
