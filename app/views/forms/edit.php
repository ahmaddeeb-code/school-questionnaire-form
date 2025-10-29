<section>
    <h2>Edit Form</h2>
    <form method="POST" action="/forms/<?= $form['id'] ?>">
        <?= $csrf ?>
        <label>Title
            <input type="text" name="title" value="<?= htmlspecialchars($form['title']) ?>" required>
        </label>
        <label>Description
            <textarea name="description"><?= htmlspecialchars($form['description']) ?></textarea>
        </label>
        <label>Status
            <select name="status">
                <option value="draft" <?= $form['status'] === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= $form['status'] === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </label>
        <label><input type="checkbox" name="allow_anonymous" <?= $form['allow_anonymous'] ? 'checked' : '' ?>> Allow anonymous responses</label>
        <label><input type="checkbox" name="allow_edits" <?= $form['allow_edits'] ? 'checked' : '' ?>> Allow edits</label>
        <label>Target audience
            <select name="target_audience">
                <option value="families" <?= $form['target_audience'] === 'families' ? 'selected' : '' ?>>Families</option>
                <option value="employees" <?= $form['target_audience'] === 'employees' ? 'selected' : '' ?>>Employees</option>
                <option value="both" <?= $form['target_audience'] === 'both' ? 'selected' : '' ?>>Families & Employees</option>
            </select>
        </label>
        <button type="submit">Update</button>
    </form>
</section>
