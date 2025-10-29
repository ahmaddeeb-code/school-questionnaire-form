<section>
    <h2><?= htmlspecialchars($form['title']) ?> Analytics</h2>
    <div class="cards">
        <div class="card">
            <h3>Total responses</h3>
            <p><?= $analytics['summary']['total'] ?></p>
        </div>
        <div class="card">
            <h3>Completed</h3>
            <p><?= $analytics['summary']['completed'] ?></p>
        </div>
    </div>
    <table>
        <thead>
            <tr>
                <th>Question</th>
                <th>Answers</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($analytics['questions'] as $question): ?>
                <tr>
                    <td><?= htmlspecialchars($question['text']) ?></td>
                    <td><?= htmlspecialchars($question['answers']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <div class="actions">
        <a class="button" href="/forms/<?= $form['id'] ?>/export/responses">Export Responses CSV</a>
        <a class="button" href="/forms/<?= $form['id'] ?>/export/analytics">Export Analytics CSV</a>
    </div>
</section>
