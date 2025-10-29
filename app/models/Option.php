<?php
namespace App\Models;

use PDO;

class Option extends BaseModel
{
    public function getByQuestion(int $questionId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM question_options WHERE question_id = :question_id ORDER BY position ASC');
        $stmt->execute(['question_id' => $questionId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): void
    {
        $stmt = $this->pdo->prepare('INSERT INTO question_options (question_id, label, value, position) VALUES (:question_id, :label, :value, :position)');
        $stmt->execute([
            'question_id' => $data['question_id'],
            'label' => $data['label'],
            'value' => $data['value'],
            'position' => $data['position'] ?? 0,
        ]);
    }
}
