<section class="respond">
    <h2><?= htmlspecialchars($form['title']) ?></h2>
    <p><?= htmlspecialchars($form['description']) ?></p>
    <form method="POST" action="/respond/<?= $token ?>" enctype="multipart/form-data" id="response-form">
        <?= $csrf ?>
        <input type="hidden" name="duration" id="duration" value="0">
        <div class="progress"><div class="progress-bar" id="progress-bar"></div></div>
        <?php foreach ($questions as $index => $question): ?>
            <fieldset class="question" data-question-id="<?= $question['id'] ?>" data-logic='<?= htmlspecialchars(json_encode($question['settings']['logic'] ?? []), ENT_QUOTES, 'UTF-8') ?>'>
                <legend><?= ($index + 1) ?>. <?= htmlspecialchars($question['text']) ?> <?= $question['required'] ? '*' : '' ?></legend>
                <?php if ($question['type'] === 'short_text'): ?>
                    <input type="text" name="answers[<?= $question['id'] ?>]" placeholder="<?= htmlspecialchars($question['settings']['placeholder'] ?? '') ?>" <?= $question['required'] ? 'required' : '' ?>>
                <?php elseif ($question['type'] === 'long_text'): ?>
                    <textarea name="answers[<?= $question['id'] ?>]" <?= $question['required'] ? 'required' : '' ?>></textarea>
                <?php elseif ($question['type'] === 'multiple_choice'): ?>
                    <?php foreach ($question['options'] as $option): ?>
                        <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="<?= htmlspecialchars($option['value']) ?>" <?= $question['required'] ? 'required' : '' ?>> <?= htmlspecialchars($option['label']) ?></label>
                    <?php endforeach; ?>
                <?php elseif ($question['type'] === 'checkbox'): ?>
                    <?php foreach ($question['options'] as $option): ?>
                        <label><input type="checkbox" name="answers[<?= $question['id'] ?>][]" value="<?= htmlspecialchars($option['value']) ?>"> <?= htmlspecialchars($option['label']) ?></label>
                    <?php endforeach; ?>
                <?php elseif ($question['type'] === 'dropdown'): ?>
                    <select name="answers[<?= $question['id'] ?>]" <?= $question['required'] ? 'required' : '' ?>>
                        <option value="">Select</option>
                        <?php foreach ($question['options'] as $option): ?>
                            <option value="<?= htmlspecialchars($option['value']) ?>"><?= htmlspecialchars($option['label']) ?></option>
                        <?php endforeach; ?>
                    </select>
                <?php elseif ($question['type'] === 'linear_scale'): ?>
                    <div class="scale">
                        <?php for ($i = 1; $i <= 5; $i++): ?>
                            <label><input type="radio" name="answers[<?= $question['id'] ?>]" value="<?= $i ?>" <?= $question['required'] ? 'required' : '' ?>> <?= $i ?></label>
                        <?php endfor; ?>
                    </div>
                <?php elseif ($question['type'] === 'date'): ?>
                    <input type="date" name="answers[<?= $question['id'] ?>]" <?= $question['required'] ? 'required' : '' ?>>
                <?php elseif ($question['type'] === 'file'): ?>
                    <input type="file" name="uploads[<?= $question['id'] ?>]" accept="image/*,application/pdf" <?= $question['required'] ? 'required' : '' ?>>
                <?php endif; ?>
                <?php if (!empty($question['description'])): ?>
                    <p class="description"><?= htmlspecialchars($question['description']) ?></p>
                <?php endif; ?>
            </fieldset>
        <?php endforeach; ?>
        <button type="submit">Submit</button>
    </form>
</section>
<script src="/js/respond.js"></script>
