<section class="auth">
    <h2><?= htmlspecialchars($title ?? 'Login') ?></h2>
    <?php if (!empty($error)): ?>
        <div class="alert"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>
    <form method="POST" action="/login">
        <?= $csrf ?>
        <label>
            Email
            <input type="email" name="email" required>
        </label>
        <label>
            Password
            <input type="password" name="password" required>
        </label>
        <button type="submit">Login</button>
    </form>
</section>
