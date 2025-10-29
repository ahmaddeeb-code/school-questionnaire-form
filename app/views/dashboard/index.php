<section class="dashboard">
    <h2><?= htmlspecialchars($user['name'] ?? 'User') ?> Dashboard</h2>
    <div class="cards">
        <?php foreach ($metrics as $label => $value): ?>
            <div class="card">
                <h3><?= ucfirst(str_replace('_', ' ', $label)) ?></h3>
                <p><?= htmlspecialchars($value) ?></p>
            </div>
        <?php endforeach; ?>
    </div>
    <h3>Your Forms</h3>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Updated</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($forms as $form): ?>
                <tr>
                    <td><a href="/forms/<?= $form['id'] ?>/builder"><?= htmlspecialchars($form['title']) ?></a></td>
                    <td><?= htmlspecialchars($form['status']) ?></td>
                    <td><?= htmlspecialchars($form['updated_at'] ?? '') ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
