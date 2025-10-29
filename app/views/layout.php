<?php
use App\Helpers\I18n;
use App\Helpers\Auth;
$direction = I18n::isRtl() ? 'rtl' : 'ltr';
$locale = I18n::locale();
$user = Auth::user();
?>
<!DOCTYPE html>
<html lang="<?= htmlspecialchars($locale) ?>" dir="<?= $direction ?>">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($title ?? 'School Forms') ?></title>
    <link rel="stylesheet" href="/css/styles.css">
</head>
<body class="layout">
    <header class="topbar">
        <h1>School Surveys</h1>
        <nav>
            <a href="/locale/en">EN</a>
            <a href="/locale/ar">AR</a>
            <?php if ($user): ?>
                <a href="/dashboard">Dashboard</a>
                <a href="/forms">Forms</a>
                <a href="/logout">Logout</a>
            <?php else: ?>
                <a href="/login">Login</a>
            <?php endif; ?>
        </nav>
    </header>
    <main class="content">
        <?php include __DIR__ . '/' . $viewFile . '.php'; ?>
    </main>
</body>
</html>
