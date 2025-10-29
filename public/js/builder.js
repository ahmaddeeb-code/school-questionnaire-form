document.addEventListener('DOMContentLoaded', () => {
    const list = document.getElementById('question-list');
    const csrfToken = document.getElementById('csrf-token');
    if (!list) return;

    const reorder = () => {
        const positions = {};
        [...list.querySelectorAll('.question')].forEach((item, index) => {
            positions[item.dataset.id] = index + 1;
        });
        const formId = list.dataset.form;
        const formData = new FormData();
        formData.append('_csrf_token', csrfToken ? csrfToken.value : '');
        Object.keys(positions).forEach(id => {
            formData.append(`positions[${id}]`, positions[id]);
        });
        fetch(`/forms/${formId}/reorder`, {
            method: 'POST',
            body: formData,
        });
    };

    list.addEventListener('click', (event) => {
        const up = event.target.closest('.move-up');
        const down = event.target.closest('.move-down');
        if (!up && !down) return;
        const question = event.target.closest('.question');
        if (!question) return;
        if (up) {
            const prev = question.previousElementSibling;
            if (prev) {
                list.insertBefore(question, prev);
                reorder();
            }
        }
        if (down) {
            const next = question.nextElementSibling;
            if (next) {
                list.insertBefore(next, question);
                reorder();
            }
        }
    });
});
