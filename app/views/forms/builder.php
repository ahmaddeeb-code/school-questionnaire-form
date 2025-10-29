<section class="builder">
    <header class="builder-header">
        <h2><?= htmlspecialchars($form['title']) ?> Builder</h2>
        <a class="button" href="/forms">Back</a>
    </header>
    <input type="hidden" id="csrf-token" value="<?= htmlspecialchars($csrfToken ?? '') ?>">
    <div class="questions" id="question-list" data-form="<?= $form['id'] ?>">
        <?php foreach ($questions as $question): ?>
            <article class="question" data-id="<?= $question['id'] ?>">
                <header>
                    <strong><?= htmlspecialchars($question['text']) ?></strong>
                    <span><?= htmlspecialchars($question['type']) ?></span>
                    <div class="question-actions">
                        <button type="button" class="move-up" aria-label="Move up">▲</button>
                        <button type="button" class="move-down" aria-label="Move down">▼</button>
                    </div>
                </header>
                <p><?= htmlspecialchars($question['description']) ?></p>
                <?php if (!empty($question['options'])): ?>
                    <ul>
                        <?php foreach ($question['options'] as $option): ?>
                            <li><?= htmlspecialchars($option['label']) ?></li>
                        <?php endforeach; ?>
                    </ul>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </div>
    <form class="add-question" method="POST" action="/forms/<?= $form['id'] ?>/questions">
        <?= $csrf ?>
        <label>Question text
            <input type="text" name="text" required>
        </label>
        <label>Type
            <select name="type">
                <option value="short_text">Short text</option>
                <option value="long_text">Long text</option>
                <option value="multiple_choice">Multiple choice</option>
                <option value="checkbox">Checkboxes</option>
                <option value="dropdown">Dropdown</option>
                <option value="linear_scale">Linear scale</option>
                <option value="date">Date</option>
                <option value="file">File upload</option>
            </select>
        </label>
        <label><input type="checkbox" name="required"> Required</label>
        <label>Options (one per line for choice questions)
            <textarea name="options_text" rows="4"></textarea>
        </label>
        <button type="submit">Add question</button>
    </form>
</section>
<script src="/js/builder.js"></script>
