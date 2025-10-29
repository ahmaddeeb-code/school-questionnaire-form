document.addEventListener('DOMContentLoaded', () => {
    const form = document.getElementById('response-form');
    if (!form) return;
    const progressBar = document.getElementById('progress-bar');
    const durationInput = document.getElementById('duration');
    const token = window.location.pathname.split('/').pop();
    const storageKey = `form-draft-${token}`;
    let seconds = 0;

    const evaluateLogic = () => {
        const questions = form.querySelectorAll('.question');
        questions.forEach(question => {
            const logicRaw = question.dataset.logic;
            if (!logicRaw) return;
            let logic;
            try {
                logic = JSON.parse(logicRaw);
            } catch (e) {
                logic = null;
            }
            if (!logic || !logic.when) return;
            const trigger = form.querySelector(`[name="answers[${logic.when.question}]"]`);
            if (!trigger) return;
            const value = trigger.type === 'radio' ? form.querySelector(`[name="answers[${logic.when.question}]"]:checked`)?.value : trigger.value;
            if (logic.when.equals && value !== logic.when.equals) {
                question.hidden = true;
            } else {
                question.hidden = false;
            }
        });
    };

    const updateProgress = () => {
        const requiredFields = [...form.querySelectorAll('[required]')];
        const answered = requiredFields.filter(field => {
            if (field.type === 'radio') {
                const group = form.querySelectorAll(`[name="${field.name}"]`);
                return [...group].some(item => item.checked);
            }
            if (field.type === 'checkbox') {
                const group = form.querySelectorAll(`[name="${field.name}"]`);
                return [...group].some(item => item.checked);
            }
            return field.value && field.value !== '';
        });
        const percent = requiredFields.length ? Math.round((answered.length / requiredFields.length) * 100) : 0;
        if (progressBar) {
            progressBar.style.width = percent + '%';
        }
    };

    const autosave = () => {
        const formData = new FormData(form);
        const plain = {};
        formData.forEach((value, key) => {
            if (plain[key]) {
                if (!Array.isArray(plain[key])) {
                    plain[key] = [plain[key]];
                }
                plain[key].push(value);
            } else {
                plain[key] = value;
            }
        });
        localStorage.setItem(storageKey, JSON.stringify(plain));
    };

    const restore = () => {
        const saved = localStorage.getItem(storageKey);
        if (!saved) return;
        const plain = JSON.parse(saved);
        Object.keys(plain).forEach(key => {
            const value = plain[key];
            const fields = form.querySelectorAll(`[name="${key}"]`);
            fields.forEach(field => {
                if (field.type === 'checkbox' || field.type === 'radio') {
                    const values = Array.isArray(value) ? value : [value];
                    field.checked = values.includes(field.value);
                } else {
                    field.value = Array.isArray(value) ? value[0] : value;
                }
            });
        });
        updateProgress();
    };

    restore();
    evaluateLogic();

    form.addEventListener('input', () => {
        evaluateLogic();
        updateProgress();
        autosave();
    });

    setInterval(() => {
        seconds += 1;
        if (durationInput) {
            durationInput.value = seconds;
        }
    }, 1000);
});
