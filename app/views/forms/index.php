<section>
    <div class="toolbar">
        <h2>Forms</h2>
        <a class="button" href="/forms/create">New Form</a>
    </div>
    <table>
        <thead>
            <tr>
                <th>Title</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($forms as $form): ?>
                <tr>
                    <td><?= htmlspecialchars($form['title']) ?></td>
                    <td><?= htmlspecialchars($form['status']) ?></td>
                    <td>
                        <a href="/forms/<?= $form['id'] ?>/edit">Edit</a>
                        <a href="/forms/<?= $form['id'] ?>/builder">Builder</a>
                        <a href="/forms/<?= $form['id'] ?>/assign">Assign</a>
                        <a href="/forms/<?= $form['id'] ?>/analytics">Analytics</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</section>
