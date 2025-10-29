<?php
return [
    'db' => [
        'host' => '127.0.0.1',
        'port' => 3306,
        'database' => 'school_forms',
        'username' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
    'app' => [
        'base_url' => '/',
        'storage_path' => __DIR__ . '/storage',
        'upload_path' => __DIR__ . '/storage/uploads',
        'locale_default' => 'en',
        'locales' => ['en', 'ar'],
        'csrf_token_name' => '_csrf_token',
        'token_rate_limit' => 5,
    ],
];
