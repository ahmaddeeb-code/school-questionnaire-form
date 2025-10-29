<section>
    <h2>Assign <?= htmlspecialchars($form['title']) ?></h2>
    <form method="POST" action="/forms/<?= $form['id'] ?>/assign">
        <?= $csrf ?>
        <label>Schedule at
            <input type="datetime-local" name="scheduled_at">
        </label>
        <label>Expires at
            <input type="datetime-local" name="expires_at">
        </label>
        <fieldset>
            <legend>Audiences</legend>
            <?php foreach ($audiences as $audience): ?>
                <label>
                    <input type="checkbox" name="audiences[]" value="<?= $audience['id'] ?>">
                    <?= htmlspecialchars($audience['name']) ?> (<?= htmlspecialchars($audience['type']) ?>)
                </label>
            <?php endforeach; ?>
        </fieldset>
        <label><input type="checkbox" name="send_emails"> Send email notifications</label>
        <button type="submit">Assign</button>
    </form>
    <h3>Existing assignments</h3>
    <ul>
        <?php foreach ($assignments as $assignment): ?>
            <li><?= htmlspecialchars($assignment['name']) ?> — <?= htmlspecialchars($assignment['scheduled_at']) ?></li>
        <?php endforeach; ?>
    </ul>
</section>
