<?php
namespace App\Models;

use PDO;

class Question extends BaseModel
{
    public function getByForm(int $formId): array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM questions WHERE form_id = :form_id ORDER BY position ASC');
        $stmt->execute(['form_id' => $formId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function create(array $data): int
    {
        $stmt = $this->pdo->prepare('INSERT INTO questions (form_id, type, text, description, required, settings, position) VALUES (:form_id, :type, :text, :description, :required, :settings, :position)');
        $stmt->execute([
            'form_id' => $data['form_id'],
            'type' => $data['type'],
            'text' => $data['text'],
            'description' => $data['description'],
            'required' => $data['required'] ?? 0,
            'settings' => json_encode($data['settings'] ?? []),
            'position' => $data['position'] ?? 0,
        ]);
        return (int)$this->pdo->lastInsertId();
    }

    public function updatePosition(int $id, int $position): void
    {
        $stmt = $this->pdo->prepare('UPDATE questions SET position = :position WHERE id = :id');
        $stmt->execute(['position' => $position, 'id' => $id]);
    }
}
