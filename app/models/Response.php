<?php
namespace App\Models;

use PDO;

class Response extends BaseModel
{
    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO responses (form_id, recipient_id, token_id, submitted_at, duration_seconds, is_complete) VALUES (:form_id, :recipient_id, :token_id, :submitted_at, :duration_seconds, :is_complete)');
        $stmt->execute([
            'form_id' => $data['form_id'],
            'recipient_id' => $data['recipient_id'],
            'token_id' => $data['token_id'],
            'submitted_at' => $data['submitted_at'],
            'duration_seconds' => $data['duration_seconds'],
            'is_complete' => $data['is_complete'],
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function saveAnswer(array $data): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO response_answers (response_id, question_id, answer_text, answer_option_id, answer_numeric, answer_date, answer_file) VALUES (:response_id, :question_id, :answer_text, :answer_option_id, :answer_numeric, :answer_date, :answer_file)');
        $stmt->execute($data);
    }

    public function analyticsSummary(int $formId): array
    {
        $summaryStmt = $this->pdo->prepare('SELECT COUNT(*) as total, SUM(is_complete) as completed FROM responses WHERE form_id = :form_id');
        $summaryStmt->execute(['form_id' => $formId]);
        $summary = $summaryStmt->fetch(PDO::FETCH_ASSOC) ?: ['total' => 0, 'completed' => 0];

        $questionStmt = $this->pdo->prepare('SELECT q.id, q.text, q.type, COUNT(ra.id) as answers FROM questions q LEFT JOIN response_answers ra ON q.id = ra.question_id WHERE q.form_id = :form_id GROUP BY q.id');
        $questionStmt->execute(['form_id' => $formId]);
        $questions = $questionStmt->fetchAll(PDO::FETCH_ASSOC);

        return ['summary' => $summary, 'questions' => $questions];
    }
}
