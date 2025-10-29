<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\I18n;
use PDO;

class LocaleController extends BaseController
{
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
    }

    public function switch(string $locale): void
    {
        $config = require __DIR__ . '/../../config.php';
        I18n::init($locale, $config['app']['locales'], $config['app']['locale_default']);
        header('Location: ' . ($_SERVER['HTTP_REFERER'] ?? '/'));
        exit;
    }
}
