<section>
    <h2><?= htmlspecialchars($title ?? 'Create form') ?></h2>
    <form method="POST" action="/forms">
        <?= $csrf ?>
        <label>Title
            <input type="text" name="title" required>
        </label>
        <label>Description
            <textarea name="description"></textarea>
        </label>
        <label>Status
            <select name="status">
                <option value="draft">Draft</option>
                <option value="published">Published</option>
            </select>
        </label>
        <label><input type="checkbox" name="allow_anonymous"> Allow anonymous responses</label>
        <label><input type="checkbox" name="allow_edits"> Allow edits</label>
        <label>Target audience
            <select name="target_audience">
                <option value="families">Families</option>
                <option value="employees">Employees</option>
                <option value="both">Families & Employees</option>
            </select>
        </label>
        <button type="submit">Save</button>
    </form>
</section>
