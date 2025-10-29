<?php
namespace App\Controllers;

use App\Controllers\BaseController;
use App\Helpers\Csrf;
use App\Helpers\I18n;
use App\Helpers\Uploads;
use App\Models\Form;
use App\Models\Question;
use App\Models\Option;
use App\Models\Response;
use App\Models\Token;
use App\Models\Upload;
use PDO;

class ResponseController extends BaseController
{
    private Form $forms;
    private Question $questions;
    private Option $options;
    private Response $responses;
    private Token $tokens;
    private Upload $uploads;

    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->forms = new Form($pdo);
        $this->questions = new Question($pdo);
        $this->options = new Option($pdo);
        $this->responses = new Response($pdo);
        $this->tokens = new Token($pdo);
        $this->uploads = new Upload($pdo);
    }

    public function show(string $token): void
    {
        $config = require __DIR__ . '/../../config.php';
        $record = $this->tokens->validate($token, $config['app']['token_rate_limit']);
        if (!$record) {
            http_response_code(403);
            echo 'Invalid token';
            return;
        }
        $form = $this->forms->find((int)$record['form_id']);
        if ($record['used_at'] && empty($form['allow_edits'])) {
            http_response_code(403);
            echo 'Token already used';
            return;
        }
        $questions = $this->questions->getByForm((int)$record['form_id']);
        foreach ($questions as &$question) {
            $question['options'] = $this->options->getByQuestion($question['id']);
        }
        $this->view('respond/form', [
            'form' => $form,
            'questions' => $questions,
            'token' => $token,
            'csrf' => Csrf::field(),
        ]);
    }

    public function submit(string $token): void
    {
        if (!Csrf::validate($_POST['_csrf_token'] ?? '')) {
            http_response_code(400);
            echo 'Invalid CSRF token';
            return;
        }
        $config = require __DIR__ . '/../../config.php';
        $record = $this->tokens->validate($token, $config['app']['token_rate_limit']);
        if (!$record) {
            http_response_code(403);
            echo 'Invalid token';
            return;
        }
        $form = $this->forms->find((int)$record['form_id']);
        if ($record['used_at'] && empty($form['allow_edits'])) {
            http_response_code(403);
            echo 'Token already used';
            return;
        }
        $answers = $_POST['answers'] ?? [];
        $responseId = $this->responses->create([
            'form_id' => $form['id'],
            'recipient_id' => (int)$record['recipient_id'],
            'token_id' => (int)$record['id'],
            'submitted_at' => date('Y-m-d H:i:s'),
            'duration_seconds' => (int)($_POST['duration'] ?? 0),
            'is_complete' => 1,
        ]);
        foreach ($answers as $questionId => $answer) {
            $payload = [
                'response_id' => $responseId,
                'question_id' => (int)$questionId,
                'answer_text' => is_array($answer) ? json_encode($answer) : $answer,
                'answer_option_id' => null,
                'answer_numeric' => null,
                'answer_date' => null,
                'answer_file' => null,
            ];
            $this->responses->saveAnswer($payload);
        }
        if (!empty($_FILES['uploads'])) {
            foreach ($_FILES['uploads']['name'] as $questionId => $name) {
                $fileInfo = [
                    'name' => $name,
                    'type' => $_FILES['uploads']['type'][$questionId],
                    'tmp_name' => $_FILES['uploads']['tmp_name'][$questionId],
                    'error' => $_FILES['uploads']['error'][$questionId],
                    'size' => $_FILES['uploads']['size'][$questionId],
                ];
                $stored = Uploads::handle($fileInfo, $config['app']['upload_path']);
                if ($stored) {
                    $this->uploads->save([
                        'response_id' => $responseId,
                        'question_id' => (int)$questionId,
                        'filename_original' => $fileInfo['name'],
                        'filename_stored' => $stored,
                        'mime_type' => $fileInfo['type'],
                        'size' => $fileInfo['size'],
                    ]);
                }
            }
        }
        if (empty($form['allow_edits'])) {
            $this->tokens->markUsed((int)$record['id']);
        }
        $message = I18n::translate('respond.thanks');
        $this->view('respond/thanks', compact('message'));
    }
}
