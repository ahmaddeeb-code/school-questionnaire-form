<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Csrf;
use App\Models\Form;
use App\Models\Question;
use App\Models\Option;
use PDO;

class BuilderController extends BaseController
{
    private Form $forms;
    private Question $questions;
    private Option $options;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->forms = new Form($pdo);
        $this->questions = new Question($pdo);
        $this->options = new Option($pdo);
    }

    public function builder(int $id): void
    {
        $form = $this->forms->find($id);
        $questions = $this->questions->getByForm($id);
        foreach ($questions as &$question) {
            $question['options'] = $this->options->getByQuestion($question['id']);
        }
        $this->view('forms/builder', [
            'form' => $form,
            'questions' => $questions,
            'csrf' => Csrf::field(),
            'csrfToken' => Csrf::token(),
        ]);
    }

    public function addQuestion(int $id): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? '')) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return;
        }
        $questionId = $this->questions->create([
            'form_id' => $id,
            'type' => $_POST['type'] ?? 'short_text',
            'text' => $_POST['text'] ?? 'New question',
            'description' => $_POST['description'] ?? '',
            'required' => isset($_POST['required']) ? 1 : 0,
            'settings' => [
                'placeholder' => $_POST['placeholder'] ?? '',
                'min_length' => $_POST['min_length'] ?? null,
                'max_length' => $_POST['max_length'] ?? null,
                'logic' => $_POST['logic'] ?? [],
            ],
            'position' => (int)($_POST['position'] ?? 0),
        ]);
        if (!empty($_POST['options_text'])) {
            $lines = array_filter(array_map('trim', explode(PHP_EOL, $_POST['options_text'])));
            $position = 0;
            foreach ($lines as $line) {
                $this->options->create([
                    'question_id' => $questionId,
                    'label' => $line,
                    'value' => strtolower(preg_replace('/\s+/', '_', $line)),
                    'position' => $position++,
                ]);
            }
        }
        header('Location: /forms/' . $id . '/builder');
        exit;
    }

    public function reorder(int $id): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? '')) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return;
        }
        $positions = $_POST['positions'] ?? [];
        foreach ($positions as $questionId => $position) {
            $this->questions->updatePosition((int)$questionId, (int)$position);
        }
        header('Location: /forms/' . $id . '/builder');
        exit;
    }
}
